namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class AccountApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['firebase'];
    }

    public function toFirebase(object $notifiable): void
    {
        $fcmToken = $notifiable->fcm_token;

        if (!$fcmToken) {
            return;
        }

        Http::withToken(config('services.firebase.access_token'))
            ->post("https://fcm.googleapis.com/v1/projects/".config('services.firebase.project_id')."/messages:send", [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => 'Conta Aprovada! 🎉',
                        'body' => 'Parabéns, seu perfil no IntelbrasTech foi homologado.'
                    ],
                    'data' => [
                        'action_url' => '/dashboard',
                        'type' => 'account_status'
                    ]
                ]
            ]);
    }
}