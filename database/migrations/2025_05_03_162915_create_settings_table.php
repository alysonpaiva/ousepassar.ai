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
        Schema::create("settings", function (Blueprint $table) {
            $table->id();
            $table->string("key")->unique(); // Chave da configuração (ex: APP_NAME, OPENAI_API_KEY)
            $table->text("value")->nullable(); // Valor da configuração
            $table->timestamps(); // Opcional: para rastrear quando foi alterado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("settings");
    }
};

