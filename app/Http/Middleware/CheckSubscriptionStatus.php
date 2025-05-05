<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscription; // Certifique-se de que o namespace do seu model está correto
use App\Models\User; // Importar o model User

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Verifica se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route("login");
        }

        // 2. Obtém o usuário autenticado e carrega seu papel (role)
        // Assumindo que:
        //   - A coluna na tabela `users` se chama `role` e armazena o ID.
        //   - Você tem um relacionamento `role()` no seu model `User` que busca o model `Role`.
        //   - O model `Role` tem uma coluna `name` com os nomes dos papéis (ex: "Administrador", "Master").
        $user = Auth::user()->load("roleInfo"); // Eager load para eficiência

        // 3. Verifica se o usuário tem um papel e se o nome é Administrador ou Master
        if ($user->role && in_array($user->role, [1, 2])) {
            // Se for Admin ou Master, libera o acesso imediatamente
            return $next($request);
        }

        // 4. Se NÃO for Admin/Master, verifica a assinatura ativa
        $hasActiveSubscription = UserSubscription::where("user_id", $user->id)
            ->where("status", "active")
            ->exists();

        // 5. Se não tiver assinatura ativa, redireciona ou retorna erro
        if (!$hasActiveSubscription) {
            // Opção A: Redirecionar para uma rota específica (ex: página de assinatura)
            // return redirect()->route("subscription.page")->with("error", "Você precisa de uma assinatura ativa para acessar esta página.");

            // Opção B: Redirecionar para o dashboard com uma mensagem de erro
            // return redirect()->route("dashboard")->with("error", "Acesso negado. Assinatura não está ativa.");

            // Opção C: Retornar erro 403 (Acesso Negado)
            abort(403, "Acesso não autorizado. Assinatura inativa ou inexistente.");
        }

        // 6. Se tiver assinatura ativa (e não for Admin/Master), permite continuar
        return $next($request);
    }
}
