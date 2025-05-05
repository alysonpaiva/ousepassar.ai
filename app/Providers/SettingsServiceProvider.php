<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Verificar se a tabela settings existe antes de tentar carregar
        // Isso evita erros durante as migrações iniciais
        try {
            if (Schema::hasTable("settings")) {
                $settings = Setting::all();

                foreach ($settings as $setting) {
                    // Define a configuração usando a chave do banco de dados
                    // Ex: config(["settings.APP_NAME" => "Meu App"])
                    Config::set("settings." . $setting->key, $setting->value);
                }
            } else {
                // Logar se a tabela não existir (apenas informativo durante o boot normal)
                // Log::info("Tabela settings não encontrada durante o boot do SettingsServiceProvider.");
            }
        } catch (\Exception $e) {
            // Logar qualquer erro que ocorra ao tentar acessar o banco de dados
            Log::error("Erro ao carregar configurações do banco de dados no SettingsServiceProvider: " . $e->getMessage());
        }
    }
}

