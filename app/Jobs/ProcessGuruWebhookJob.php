<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserSubscription; // Precisaremos criar este model
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessGuruWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    /**
     * Create a new job instance.
     *
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info("Processando Job ProcessGuruWebhookJob", ["transaction_id" => $this->payload["id"] ?? "N/A"]);

        // Extrair dados relevantes do payload
        $transactionStatus = $this->payload["status"] ?? null;
        $contactData = $this->payload["contact"] ?? null;
        $subscriptionData = $this->payload["subscription"] ?? null; // Presente no webhook de transação
        $transactionId = $this->payload["id"] ?? null;
        $productId = $this->payload["product"]["internal_id"] ?? ($this->payload["items"][0]["internal_id"] ?? null); // Tenta pegar do product ou do primeiro item
        $productName = $this->payload["product"]["name"] ?? ($this->payload["items"][0]["name"] ?? null);

        // --- Validações Essenciais ---
        if (!$transactionStatus || !$contactData || !$transactionId) {
            Log::error("Payload do webhook Guru incompleto ou inválido.", ["payload" => $this->payload]);
            // Não tentar novamente, pois o payload está quebrado
            $this->fail(new \Exception("Payload incompleto ou inválido."));
            return;
        }

        $userEmail = $contactData["email"] ?? null;
        $userName = $contactData["name"] ?? "Usuário Guru"; // Usar um nome padrão se não vier

        if (!$userEmail) {
            Log::error("Email do contato não encontrado no payload do webhook Guru.", ["payload" => $this->payload]);
            $this->fail(new \Exception("Email do contato não encontrado."));
            return;
        }

        // --- Lógica Principal ---
        try {
            // 1. Encontrar ou Criar Usuário
            $user = User::firstOrCreate(
                ["email" => $userEmail],
                [
                    "name" => $userName,
                    "password" => Hash::make(Str::random(16)), // Gera senha aleatória segura
                    // Adicione outros campos necessários para seu modelo User, ex: role_id
                    // "role_id" => 2, // Exemplo: ID para "Aluno"
                ]
            );

            if ($user->wasRecentlyCreated) {
                Log::info("Novo usuário criado via webhook Guru", ["user_id" => $user->id, "email" => $userEmail]);
                // TODO: Opcional - Enviar email de boas-vindas com a senha ou link para definir senha
            }

            // 2. Atualizar/Criar Registro de Assinatura/Transação
            // Usar transaction_id como chave única para idempotência
            $subscriptionRecord = UserSubscription::updateOrCreate(
                ["guru_transaction_id" => $transactionId],
                [
                    "user_id" => $user->id,
                    "guru_subscription_id" => $subscriptionData["internal_id"] ?? ($this->payload["internal_id"] ?? null), // Pega do sub-objeto ou do payload principal (webhook de assinatura)
                    "guru_product_id" => $productId,
                    "guru_plan_name" => $productName,
                    "status" => $this->mapGuruStatus($transactionStatus), // Mapear status do Guru para status interno
                    "started_at" => isset($this->payload["dates"]["confirmed_at"]) ? date("Y-m-d H:i:s", strtotime($this->payload["dates"]["confirmed_at"])) : null,
                    "expires_at" => $this->calculateExpiryDate($this->payload), // Calcular data de expiração
                    "canceled_at" => isset($this->payload["dates"]["canceled_at"]) ? date("Y-m-d H:i:s", strtotime($this->payload["dates"]["canceled_at"])) : null,
                    "last_event_at" => now(),
                    "webhook_payload" => json_encode($this->payload) // Guardar payload para debug
                ]
            );

            Log::info("Registro UserSubscription atualizado/criado", ["user_subscription_id" => $subscriptionRecord->id, "status" => $subscriptionRecord->status]);

            // TODO: Adicionar lógica adicional se necessário (ex: disparar outros eventos)

        } catch (\Exception $e) {
            Log::error("Erro ao processar webhook Guru no Job: " . $e->getMessage(), [
                "exception" => $e,
                "payload" => $this->payload
            ]);
            // Tentar novamente o Job (Laravel fará isso automaticamente baseado na config da fila)
            $this->release(60); // Tentar novamente em 60 segundos (exemplo)
        }
    }

    /**
     * Mapeia o status recebido do Guru para um status interno padronizado.
     *
     * @param string|null $guruStatus
     * @return string
     */
    private function mapGuruStatus(?string $guruStatus): string
    {
        // Adapte este mapeamento conforme os status do Guru e os que você quer usar internamente
        switch (strtolower($guruStatus ?? "")) {
            case "approved":
            case "active": // Status de assinatura
                return "active";
            case "canceled":
            case "expired":
                return "canceled";
            case "pending":
            case "waiting":
                return "pending";
            case "refunded":
                return "refunded";
            case "failed":
            case "refused":
                return "failed";
            default:
                return "unknown";
        }
    }

    /**
     * Calcula a data de expiração baseada nos dados do payload.
     *
     * @param array $payload
     * @return string|null
     */
    private function calculateExpiryDate(array $payload): ?string
    {
        // Prioriza a data de expiração da assinatura, se disponível
        if (isset($payload["dates"]["cycle_end_date"])) {
            return date("Y-m-d H:i:s", strtotime($payload["dates"]["cycle_end_date"] . " 23:59:59")); // Fim do dia
        }
        // Tenta pegar do webhook de transação (pode ter warranty_until ou unavailable_until)
        if (isset($payload["dates"]["unavailable_until"])) {
            return date("Y-m-d H:i:s", strtotime($payload["dates"]["unavailable_until"]));
        }
        if (isset($payload["dates"]["warranty_until"])) {
            return date("Y-m-d H:i:s", strtotime($payload["dates"]["warranty_until"]));
        }
        // Adicione outras lógicas se necessário (ex: calcular baseado em data de confirmação + período)
        return null;
    }

    /**
     * The job failed to process.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical("Falha definitiva no Job ProcessGuruWebhookJob", [
            "exception_message" => $exception->getMessage(),
            "payload" => $this->payload
        ]);
        // TODO: Notificar administradores sobre a falha definitiva
    }
}
