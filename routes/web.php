<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\GuruWebhookController;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return to_route('dashboard');
});

Route::post("/webhooks/guru", [GuruWebhookController::class, "handle"])->name("webhooks.guru");

Route::middleware('auth', 'subscribed')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/users', UsersController::class);

    // Exibir formulário de alteração de senha
    Route::get('/users/{user}/password', [App\Http\Controllers\UsersController::class, 'password'])->name('users.password');

    // Atualizar a senha
    Route::put('/users/{user}/password', [App\Http\Controllers\UsersController::class, 'updatePassword'])->name('users.updatePassword');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas para Categorias
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Rotas para Agentes
    Route::resource('agents', AgentController::class);
    Route::post('agents/{agent}/process', [AgentController::class, 'process'])->name('agents.process');
    Route::get('agents/{agent}/history', [AgentController::class, 'history'])->name('agents.history');
    Route::get('agents/history/{history}', [AgentController::class, 'historyDetail'])->name('agents.history.detail');
    Route::get('agents/history/data/{history}', [AgentController::class, 'getHistoryData'])->name('agents.history.data');

    // Rotas para Campos
    Route::post('agents/{agent}/fields', [FieldController::class, 'store'])->name('fields.store');
    Route::put('fields/{field}', [FieldController::class, 'update'])->name('fields.update');
    Route::delete('fields/{field}', [FieldController::class, 'destroy'])->name('fields.destroy');
    Route::post('agents/{agent}/fields/reorder', [FieldController::class, 'reorder'])->name('fields.reorder');

    // Rotas para PDF
    Route::post('pdf/generate', [PdfController::class, 'generatePdf'])->name('pdf.generate');
    Route::get('pdf/history/{historyId}', [PdfController::class, 'generatePdf'])->name('pdf.history');

    // Rodas de Configuração
    Route::get('/configuracoes', [ConfigController::class, 'index'])->name('config.index');
    Route::post('/configuracoes', [ConfigController::class, 'salvar'])->name('config.salvar');

    //ROTAS DE COMANDOS NO PHP
    Route::get('migration', function () {
        Artisan::call('migrate');
    });
});

require __DIR__ . '/auth.php';
