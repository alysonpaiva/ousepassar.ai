<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class ApiTokenController extends Controller
{
    /**
     * Criar um novo token de API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // Criar um novo token com o nome do dispositivo
        $token = $user->createToken($request->device_name);

        return response()->json([
            'success' => true,
            'token' => $token->plainTextToken,
            'message' => 'Token criado com sucesso'
        ]);
    }

    /**
     * Listar todos os tokens do usuário autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listTokens(Request $request)
    {
        $tokens = $request->user()->tokens;

        return response()->json([
            'success' => true,
            'tokens' => $tokens->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'last_used_at' => $token->last_used_at,
                    'created_at' => $token->created_at
                ];
            })
        ]);
    }

    /**
     * Revogar um token específico.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeToken(Request $request, $id)
    {
        // Verificar se o token pertence ao usuário autenticado
        $token = $request->user()->tokens()->where('id', $id)->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token não encontrado ou não pertence ao usuário autenticado'
            ], 404);
        }

        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token revogado com sucesso'
        ]);
    }

    /**
     * Revogar todos os tokens do usuário autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeAllTokens(Request $request)
    {
        // Revogar todos os tokens, exceto o token atual
        $request->user()->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Todos os outros tokens foram revogados com sucesso'
        ]);
    }

    /**
     * Revogar o token atual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeCurrentToken(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token atual revogado com sucesso'
        ]);
    }
}
