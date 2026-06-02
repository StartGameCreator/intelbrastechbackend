namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Notifications\AccountApprovedNotification;

class UserModerationController extends Controller
{
    public function pendingUsers(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request->user());
        $users = User::pending()->with('technician')->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($users);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request->user());
        $user = User::findOrFail($id);
        
        if ($user->is_active) {
            return response()->json(['message' => 'Esta conta já está ativa.'], 400);
        }

        $user->update(['is_active' => true]);
        $user->notify(new AccountApprovedNotification());

        return response()->json(['success' => true, 'message' => "A conta de {$user->name} foi aprovada."]);
    }

    public function suspend(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request->user());
        $user = User::findOrFail($id);
        $user->update(['is_active' => false]);

        return response()->json(['success' => true, 'message' => "A conta de {$user->name} foi suspensa."]);
    }

    private function authorizeAdmin(User $user): void
    {
        if (!in_array($user->role, ['master', 'regional'])) {
            abort(403, 'Acesso restrito apenas para administradores IntelbrasTech.');
        }
    }
}