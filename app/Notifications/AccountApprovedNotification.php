namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class AccountApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function via(object $notifiable): array
    {
        // Dispara o canal personalizado "firebase"
        return ['firebase'];
    }

    /**
     * Envia o push diretamente para a API do Firebase Cloud Messaging
     */
    public function toFirebase(object $notifiable): void
    {
        $fcmToken = $notifiable->fcm_token;

        // Se o usuário não tiver o aplicativo instalado ou não deu permissão de push, ignora
        if (!$fcmToken) {
            return;
        }

        // Configurações do cabeçalho de autenticação do Firebase Google API
        // Nota: Substitua pelo seu método de geração de Token OAuth2 do Google Project Credentials
        $firebaseProjectId = config('services.firebase.project_id');
        $accessToken = config('services.firebase.access_token'); 

        Http::withToken($accessToken)
            ->post("https://fcm.googleapis.com/v1/projects/{$firebaseProjectId}/messages:send", [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => 'Conta Aprovada! 🎉',
                        'body' => 'Parabéns, seu perfil no IntelbrasTech foi homologado. Você já pode receber chamados.'
                    ],
                    'data' => [
                        'action_url' => '/dashboard',
                        'type' => 'account_status'
                    ],
                    'android' => [
                        'notification' => ['icon' => 'fcm_push_icon', 'color' => '#00519E'] // Azul Intelbras
                    ]
                ]
            ]);
    }
}