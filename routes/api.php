use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\OAuthController;
use App\Http\Controllers\Api\Admin\RegionalIntelligenceController;
use App\Http\Controllers\Api\Admin\UserModerationController;
use App\Http\Controllers\Api\Auth\FcmTokenController;
use App\Http\Controllers\Api\Social\FeedController;
use App\Http\Controllers\Api\Support\MeetingController;

// Rotas de Autenticação Livres
Route::prefix('auth')->group(function () {
    Route::get('{provider}/redirect', [OAuthController::class, 'redirectToProvider'])->where('provider', 'google|microsoft');
    Route::get('{provider}/callback', [OAuthController::class, 'handleProviderCallback'])->where('provider', 'google|microsoft');
});

// Rotas Protegidas por Token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('auth/fcm-token', [FcmTokenController::class, 'updateToken']);

    Route::get('feed', [FeedController::class, 'index']);
    Route::post('posts', [FeedController::class, 'store']);
    Route::post('posts/{id}/like', [FeedController::class, 'toggleLike']);

    Route::post('tickets/{ticketId}/schedule-meeting', [MeetingController::class, 'schedule']);

    Route::prefix('admin')->group(function () {
        Route::get('heatmap', [RegionalIntelligenceController::class, 'getNationalHeatmap']);
        Route::get('users/pending', [UserModerationController::class, 'pendingUsers']);
        Route::post('users/{id}/approve', [UserModerationController::class, 'approve']);
        Route::post('users/{id}/suspend', [UserModerationController::class, 'suspend']);
    });
});