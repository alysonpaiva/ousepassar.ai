<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessGuruWebhookJob; // Importar o Job que será criado
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response; // Para retornar a resposta HTTP

class GuruWebhookController extends Controller
{
    /**
     * Handle incoming webhooks from Guru Manager.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        // 1. Validar o Token de Autenticação
        $guruToken = $request->input("api_token");
        $expectedToken = config("settings.GURU_ACCOUNT_TOKEN", env("GURU_ACCOUNT_TOKEN")); // Busca do DB com fallback no .env

        // Adicionar log para depuração (remover em produção se não necessário)
        Log::info("Webhook Guru recebido", ["token_recebido" => $guruToken]);

        if (empty($expectedToken)) {
            Log::error("Token da conta Guru (GURU_ACCOUNT_TOKEN) não configurado na aplicação.");
            // Retornar 401 ou 500? Guru não re-tenta 401. Talvez 503 Service Unavailable?
            // Vamos retornar 401 por enquanto, indicando falha de autenticação.
            return Response::json(["error" => "Configuração interna inválida."], 401);
        }

        if (!$guruToken || $guruToken !== $expectedToken) {
            Log::warning("Webhook Guru recebido com token inválido ou ausente.", ["token_recebido" => $guruToken]);
            // Retornar 401 Unauthorized, pois o Guru não re-tenta este erro.
            return Response::json(["error" => "Token inválido."], 401);
        }

        // 2. Obter todo o payload do webhook
        $payload = $request->all();

        // Adicionar log do payload completo (cuidado com dados sensíveis em produção)
        // Log::debug("Payload completo do Webhook Guru:", $payload);

        // 3. Despachar o Job para processamento em segundo plano
        try {
            ProcessGuruWebhookJob::dispatch($payload);
            Log::info("Job ProcessGuruWebhookJob despachado com sucesso.");
        } catch (\Exception $e) {
            Log::error("Erro ao despachar ProcessGuruWebhookJob: " . $e->getMessage(), ["payload" => $payload]);
            // Se não conseguir despachar o job, retornar erro 500 para o Guru tentar novamente.
            return Response::json(["error" => "Erro interno ao processar webhook."], 500);
        }

        // 4. Retornar HTTP 200 OK para o Guru imediatamente
        return Response::json(["message" => "Webhook recebido com sucesso."], 200);
    }
}
