use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\OAuthController;
use App\Http\Controllers\Api\Admin\RegionalIntelligenceController;
use App\Http\Controllers\Api\Admin\UserModerationController;
use App\Http\Controllers\Api\Auth\FcmTokenController;
use App\Http\Controllers\Api\Social\FeedController;
use App\Http\Controllers\Api\Support\MeetingController;

/*
|--------------------------------------------------------------------------
| IntelbrasTech API Centralizada - Versão Consolidada
|--------------------------------------------------------------------------
*/

// [LIVRES] Rotas de Autenticação OAuth2 / Socialite
Route::prefix('auth')->group(function () {
    Route::get('{provider}/redirect', [OAuthController::class, 'redirectToProvider'])->where('provider', 'google|microsoft');
    Route::get('{provider}/callback', [OAuthController::class, 'handleProviderCallback'])->where('provider', 'google|microsoft');
});

// [PROTEGIDAS] Exige Header 'Authorization: Bearer Token' obtido no login PWA
Route::middleware('auth:sanctum')->group(function () {
    
    // Notificações Push PWA
    Route::post('auth/fcm-token', [FcmTokenController::class, 'updateToken']);

    // Rede Social Técnica
    Route::get('feed', [FeedController::class, 'index']);
    Route::post('posts', [FeedController::class, 'store']);
    Route::post('posts/{id}/like', [FeedController::class, 'toggleLike']);

    // Suporte e Videoconferência
    Route::post('tickets/{ticketId}/schedule-meeting', [MeetingController::class, 'schedule']);

    // Administração Central (Filtro por Role interna nos Controllers)
    Route::prefix('admin')->group(function () {
        Route::get('heatmap', [RegionalIntelligenceController::class, 'getNationalHeatmap']);
        Route::get('users/pending', [UserModerationController::class, 'pendingUsers']);
        Route::post('users/{id}/approve', [UserModerationController::class, 'approve']);
        Route::post('users/{id}/suspend', [UserModerationController::class, 'suspend']);
    });
});