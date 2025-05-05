<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Usuário não autenticado.');
        }

        $userRoleId = $user->role;
        $userRoleName = optional($user->roleInfo)->name; // Protege contra null

        foreach ($roles as $role) {
            // Se for número, compara com ID
            if (is_numeric($role) && (int)$role === (int)$userRoleId) {
                return $next($request);
            }

            // Se for string, compara com nome
            if (is_string($role) && strtolower($role) === strtolower($userRoleName)) {
                return $next($request);
            }
        }

        abort(403, 'Acesso não autorizado.');
    }
}
