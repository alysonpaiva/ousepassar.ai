<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AgentController;

// Rotas públicas (se necessário)
// ...

// Rotas protegidas (requerem token de API)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('agents', [AgentController::class, 'index']);
    Route::get('agents/{id}', [AgentController::class, 'show']);
    Route::post('agents/{id}/process', [AgentController::class, 'process']);
    Route::get('agents/{id}/history', [AgentController::class, 'history']);
    Route::get('history/{id}', [AgentController::class, 'historyDetail']);
});

Route::post('/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['As credenciais fornecidas estão incorretas.'],
        ]);
    }

    return ['token' => $user->createToken($request->device_name)->plainTextToken];
});
