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
        Schema::table("user_subscriptions", function (Blueprint $table) {
            // Adiciona a coluna 'status' após a coluna 'guru_transaction_id'
            // (Corrigido: a coluna era guru_transaction_id, não transaction_id)
            // Ou podemos simplesmente remover o ->after() para adicionar no final
            $table->string("status")->after("guru_transaction_id")->nullable()->default("pending");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("user_subscriptions", function (Blueprint $table) {
            $table->dropColumn("status");
        });
    }
};

