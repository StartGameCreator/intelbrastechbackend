namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;

class OAuthController extends Controller
{
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $providerColumn = $provider . '_id';

            $user = User::where($providerColumn, $socialUser->getId())->first() 
                    ?? User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName() ?? 'Usuário IntelbrasTech',
                    'email' => $socialUser->getEmail(),
                    $providerColumn => $socialUser->getId(),
                    'role' => 'client',
                    'is_active' => false,
                ]);
            } else if (empty($user->{$providerColumn})) {
                $user->update([$providerColumn => $socialUser->getId()]);
            }

            $token = $user->createToken('intelbrastech_auth_token')->plainTextToken;
            $status = $user->is_active ? 'active' : 'pending';

            return redirect()->away(config('app.frontend_url') . "/oauth-callback?token={$token}&status={$status}");
        } catch (\Exception $e) {
            report($e);
            return redirect()->away(config('app.frontend_url') . "/login?error=auth_failed");
        }
    }
}