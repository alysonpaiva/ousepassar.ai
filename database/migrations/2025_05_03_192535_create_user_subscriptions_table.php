<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("user_subscriptions", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade"); // Chave estrangeira para users
            $table->string("guru_subscription_id")->nullable()->index(); // ID da assinatura no Guru (pode ser nulo se for compra única?)
            $table->string("guru_transaction_id")->unique(); // ID da transação no Guru (chave para idempotência?)
            $table->string("guru_product_id")->nullable()->index(); // ID do produto/plano no Guru
            $table->string("guru_plan_name")->nullable(); // Nome do plano/produto
            $table->string("status")->index(); // Status (ex: active, canceled, expired, pending, approved, etc.)
            $table->timestamp("started_at")->nullable(); // Data de início da assinatura/acesso
            $table->timestamp("expires_at")->nullable()->index(); // Data de expiração ou próximo ciclo
            $table->timestamp("canceled_at")->nullable(); // Data de cancelamento
            $table->timestamp("last_event_at")->nullable(); // Data do último webhook recebido
            $table->json("webhook_payload")->nullable(); // Opcional: guardar o último payload para debug
            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("user_subscriptions");
    }
};
