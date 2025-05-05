<?php

namespace App\Http\Controllers;

use App\Models\Setting; // Importar o model Setting
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan; // Manter para limpar cache se necessário

class ConfigController extends Controller
{
    // Chaves de configuração que este controller gerencia
    private $managedKeys = ["APP_NAME", "OPENAI_API_KEY", "OPENAI_ASSISTENTE", "APP_ENV", "GURU_ACCOUNT_TOKEN"];

    public function index()
    {
        if (Auth::user()->role != 1) {
            abort(403, "Acesso não autorizado.");
        }

        // Carrega valores atuais do banco de dados
        $settings = Setting::whereIn("key", $this->managedKeys)->pluck("value", "key");

        // Prepara os dados para a view, usando valores do .env como fallback inicial se não estiver no DB
        $viewData = [
            "app_name" => $settings->get("APP_NAME", env("APP_NAME", "Laravel")),
            "openai_key" => $settings->get("OPENAI_API_KEY", env("OPENAI_API_KEY")),
            "openai_assistente" => $settings->get("OPENAI_ASSISTENTE", env("OPENAI_ASSISTENTE")),
            "guru_key" => $settings->get("GURU_ACCOUNT_TOKEN", env("GURU_ACCOUNT_TOKEN")),
            "app_env" => $settings->get("APP_ENV", env("APP_ENV", "production")),
        ];

        return view("configuracoes", $viewData);
    }

    public function salvar(Request $request)
    {
        if (Auth::user()->role != 1) {
            abort(403, "Acesso não autorizado.");
        }

        // Validar os dados recebidos
        $validatedData = $request->validate([
            "APP_NAME" => "required|string|max:255",
            "OPENAI_API_KEY" => "required|string",
            "OPENAI_ASSISTENTE" => "required|string",
            "GURU_ACCOUNT_TOKEN" => "required|string",
            "APP_ENV" => "required|in:production,local",
        ]);

        $errorOccurred = false;

        foreach ($this->managedKeys as $key) {
            if (isset($validatedData[$key])) {
                try {
                    Setting::updateOrCreate(
                        ["key" => $key],
                        ["value" => $validatedData[$key]]
                    );
                } catch (\Exception $e) {
                    Log::error("Erro ao salvar configuração '{$key}' no banco de dados: " . $e->getMessage());
                    $errorOccurred = true;
                }
            }
        }

        // Limpar cache de configuração para refletir mudanças (opcional, mas bom se usar config())
        try {
            Artisan::call("config:clear");
            // Não é necessário config:cache aqui, pois as configurações virão do DB
        } catch (\Exception $e) {
            Log::error("Erro ao limpar config via Artisan após salvar settings: " . $e->getMessage());
            // Não bloquear o usuário por isso, apenas logar
        }

        if ($errorOccurred) {
            return redirect()->route("config.index")
                ->with("error", "Erro ao salvar uma ou mais configurações. Verifique os logs.");
        } else {
            return redirect()->route("config.index")
                ->with("success", "Configurações atualizadas com sucesso!");
        }
    }

    // O método atualizarEnv foi removido pois não é mais necessário
}
