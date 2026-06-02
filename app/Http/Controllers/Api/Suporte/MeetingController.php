namespace App\Http\Controllers\Api\Support;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\VideoConferenceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MeetingController extends Controller
{
    public function __construct(protected VideoConferenceService $conferenceService) {}

    public function schedule(Request $request, int $ticketId): JsonResponse
    {
        $request->validate([
            'platform' => 'required|in:google_meet,ms_teams',
            'scheduled_at' => 'required|date|after:now'
        ]);

        $ticket = Ticket::findOrFail($ticketId);

        if ($request->user()->id !== $ticket->technician_id && $request->user()->role !== 'master') {
            return response()->json(['error' => 'Não autorizado.'], 403);
        }

        try {
            $meeting = $this->conferenceService->createMeeting($ticket, $request->platform, $request->scheduled_at);
            return response()->json(['success' => true, 'meeting' => $meeting], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}