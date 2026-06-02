namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Notifications\AccountApprovedNotification; // Criaremos este cara no Módulo 2

class UserModerationController extends Controller
{
    /**
     * Lista todos os usuários (técnicos ou clientes) aguardando aprovação
     */
    public function pendingUsers(Request $request): JsonResponse
    {
        // Garante que apenas administradores acessem
        $this->authorizeAdmin($request->user());

        $users = User::pending()
            ->with('technician') // Traz os dados profissionais se for técnico
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($users);
    }

    /**
     * Aprova e ativa a conta de um usuário
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request->user());

        $user = User::findOrFail($id);
        
        if ($user->is_active) {
            return response()->json(['message' => 'Esta conta já está ativa.'], 400);
        }

        $user->update(['is_active' => true]);

        // Dispara a notificação de aprovação (Será processada via Fila/Queue)
        $user->notify(new AccountApprovedNotification());

        return response()->json([
            'success' => true,
            'message' => "A conta de {$user->name} foi aprovada com sucesso!"
        ]);
    }

    /**
     * Recusa/Inativa/Suspende um usuário do sistema
     */
    public function suspend(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request->user());

        $user = User::findOrFail($id);
        $user->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => "A conta de {$user->name} foi suspensa/rejeitada."
        ]);
    }

    /**
     * Validação interna de hierarquia
     */
    private function authorizeAdmin(User $user): void
    {
        if (!in_array($user->role, ['master', 'regional'])) {
            abort(403, 'Acesso restrito apenas para administradores IntelbrasTech.');
        }
    }
}