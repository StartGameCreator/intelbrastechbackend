<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\RegionalIntelligenceController;
use App\Http\Controllers\UserModerationController;

/*
|--------------------------------------------------------------------------
| API Routes - IntelbrasTech (Módulo Alfa + Proteção Spatie RBAC)
|--------------------------------------------------------------------------
*/

// 1. Rotas de Autenticação Livres (Públicas)
Route::prefix('auth')->group(function () {
    Route::get('{provider}/redirect', [OAuthController::class, 'redirectToProvider'])
        ->where('provider', 'google|microsoft');
        
    Route::get('{provider}/callback', [OAuthController::class, 'handleProviderCallback'])
        ->where('provider', 'google|microsoft');
});

// 2. Rotas Protegidas por Token Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Atualização de Token Push Notification (Qualquer perfil logado faz)
    Route::post('auth/fcm-token', [FcmTokenController::class, 'updateToken']);

    // --- REDE SOCIAL TÉCNICA ---
    Route::get('feed', [FeedController::class, 'index']);
    
    // Apenas Técnicos, Empresas ou Administradores podem publicar no feed
    Route::post('posts', [FeedController::class, 'store'])
        ->middleware('role:technician|company|master|regional');
        
    Route::post('posts/{id}/like', [FeedController::class, 'toggleLike']);

    // --- MARKETPLACE / AGENDAMENTOS ---
    // Clientes ou Empresas agendam videoconferências com os técnicos
    Route::post('tickets/{ticketId}/schedule-meeting', [MeetingController::class, 'schedule'])
        ->middleware('role:client|company|master');

    // --- PAINEL ADMINISTRATIVO (PROTEÇÃO ESTRITA SPATIE) ---
    // Bloqueia na raiz: Usuários comuns, Técnicos ou Clientes nem enxergam estas rotas
    Route::prefix('admin')->middleware(['role:master|regional'])->group(function () {
        
        // Inteligência Geográfica de Vendas/Instalações
        Route::get('heatmap', [RegionalIntelligenceController::class, 'getNationalHeatmap']);
        
        // Moderação e Homologação de novos cadastros de Técnicos/Empresas
        Route::get('users/pending', [UserModerationController::class, 'pendingUsers']);
        Route::post('users/{id}/approve', [UserModerationController::class, 'approve']);
        Route::post('users/{id}/suspend', [UserModerationController::class, 'suspend']);
        
    });
});