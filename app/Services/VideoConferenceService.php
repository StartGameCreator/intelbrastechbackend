namespace App\Services;

use App\Models\Ticket;
use App\Models\Meeting;
use Illuminate\Support\Facades\Http;
use Exception;

class VideoConferenceService
{
    public function createMeeting(Ticket $ticket, string $platform, string $scheduledAt): Meeting
    {
        $joinUrl = '';
        $externalId = null;

        if ($platform === 'google_meet') {
            $response = Http::withToken($this->getGoogleAccessToken())
                ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1', [
                    'summary' => "Suporte Técnico IntelbrasTech: Chamado #{$ticket->id}",
                    'description' => "Videoconferência para o chamado: {$ticket->title}",
                    'start' => ['dateTime' => date(DATE_RFC3339, strtotime($scheduledAt)), 'timeZone' => 'America/Sao_Paulo'],
                    'end' => ['dateTime' => date(DATE_RFC3339, strtotime($scheduledAt . ' + 1 hour')), 'timeZone' => 'America/Sao_Paulo'],
                    'conferenceData' => [
                        'createRequest' => [
                            'requestId' => 'intelbras_tech_req_' . time(),
                            'conferenceSolutionKey' => ['type' => 'hangoutsMeet']
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $joinUrl = $response->json('hangoutLink');
                $externalId = $response->json('id');
            } else {
                throw new Exception('Falha ao gerar sala no Google Meet via API.');
            }

        } else if ($platform === 'ms_teams') {
            $response = Http::withToken($this->getMicrosoftAccessToken())
                ->post('https://graph.microsoft.com/v1.0/me/onlineMeetings', [
                    'subject' => "Atendimento Remoto IntelbrasTech #{$ticket->id}",
                    'startDateTime' => date(DATE_ISO8601, strtotime($scheduledAt)),
                    'endDateTime' => date(DATE_ISO8601, strtotime($scheduledAt . ' + 1 hour')),
                ]);

            if ($response->successful()) {
                $joinUrl = $response->json('joinWebUrl');
                $externalId = $response->json('id');
            } else {
                throw new Exception('Falha ao gerar sala no Microsoft Teams via API.');
            }
        }

        return Meeting::create([
            'ticket_id' => $ticket->id,
            'platform' => $platform,
            'meeting_id' => $externalId,
            'join_url' => $joinUrl,
            'scheduled_at' => $scheduledAt
        ]);
    }

    private function getGoogleAccessToken(): string {
        return "TOKEN_DA_CONTA_MASTER_GOOGLE";
    }

    private function getMicrosoftAccessToken(): string {
        $response = Http::asForm()->post('https://login.microsoftonline.com/'.config('services.microsoft.tenant').'/oauth2/v2.0/token', [
            'client_id' => config('services.microsoft.client_id'),
            'scope' => 'https://graph.microsoft.com/.default',
            'client_secret' => config('services.microsoft.client_secret'),
            'grant_type' => 'client_credentials',
        ]);
        return $response->json('access_token');
    }
}