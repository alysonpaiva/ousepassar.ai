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
use App\Http\Controllers\ConversationController; // Adicionar import para ConversationController
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return to_route('dashboard');
});

Route::post("/webhooks/guru", [GuruWebhookController::class, "handle"])->name("webhooks.guru");

// Rotas que exigem autenticação e assinatura ativa (ou ser Admin/Master)
Route::middleware(['auth', 'subscribed'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Rotas de Usuários e Perfil ---
    Route::resource('/users', UsersController::class);
    Route::get('/users/{user}/password', [UsersController::class, 'password'])->name('users.password');
    Route::put('/users/{user}/password', [UsersController::class, 'updatePassword'])->name('users.updatePassword');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Rotas de Administração (Categorias, Agentes, Campos, Configurações) ---
    // Estas rotas podem precisar de um middleware adicional para verificar se o usuário é Admin/Master
    // Ex: Route::middleware('admin')->group(function() { ... });

    Route::resource('categories', CategoryController::class)->except(['show']);

    Route::resource('agents', AgentController::class);
    Route::post('agents/{agent}/process', [AgentController::class, 'process'])->name('agents.process'); // Usado por Agentes Prompt

    // Rotas AJAX para Histórico de Agentes Prompt (Usadas pelo JS)
    Route::get('agents/{agent}/prompt-history', [AgentController::class, 'getPromptHistory'])->name('agents.prompt.history');
    Route::get('agents/history/{history}/details', [AgentController::class, 'getHistoryDetails'])->name('agents.history.details.ajax'); // Rota AJAX para detalhes
    // Manter a rota original de detalhes se houver uma view separada
    // Route::get('agents/history/{history}', [AgentController::class, 'historyDetail'])->name('agents.history.detail');

    // Rotas para Campos (associados a Agentes)
    Route::post('agents/{agent}/fields', [FieldController::class, 'store'])->name('fields.store');
    Route::put('fields/{field}', [FieldController::class, 'update'])->name('fields.update');
    Route::delete('fields/{field}', [FieldController::class, 'destroy'])->name('fields.destroy');
    Route::post('agents/{agent}/fields/reorder', [FieldController::class, 'reorder'])->name('fields.reorder');

    // Rotas para PDF
    Route::post('pdf/generate', [PdfController::class, 'generatePdf'])->name('pdf.generate'); // PDF para Agente Prompt (via formulário)
    // Route::get('pdf/history/{historyId}', [PdfController::class, 'generatePdf'])->name('pdf.history'); // Rota antiga, pode ser removida se não usada
    Route::get('conversations/{conversation}/pdf', [PdfController::class, 'generateConversationPdf'])->name('pdf.conversation'); // PDF para Conversas

    // Rotas de Configuração
    Route::get('/configuracoes', [ConfigController::class, 'index'])->name('config.index');
    Route::post('/configuracoes', [ConfigController::class, 'salvar'])->name('config.salvar');

    // --- Rotas para Conversas (Agentes Conversacionais) ---
    // Assumindo um ConversationController, ajuste se necessário
    Route::get('agents/{agent}/conversations', [ConversationController::class, 'index'])->name('conversations.index'); // Listar conversas de um agente
    Route::post('agents/{agent}/conversations/start', [ConversationController::class, 'start'])->name('conversations.start'); // Iniciar nova conversa
    Route::get('conversations/{conversation}/messages', [ConversationController::class, 'getMessages'])->name('conversations.messages'); // Buscar mensagens de uma conversa
    Route::post('conversations/{conversation}/messages/send', [ConversationController::class, 'sendMessage'])->name('conversations.send'); // Enviar mensagem

    // --- Rotas de Comandos Artisan (Usar com cautela em produção) ---
    Route::get('migration', function () {
        // Adicionar verificação de permissão aqui (ex: if (Auth::user()->isAdmin()))
        Artisan::call('migrate');
        return 'Migration executada!';
    });
});

require __DIR__ . '/auth.php';
