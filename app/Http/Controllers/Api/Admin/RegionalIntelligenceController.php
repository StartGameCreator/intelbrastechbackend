namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technician;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RegionalIntelligenceController extends Controller
{
    public function getNationalHeatmap(): JsonResponse
    {
        $data = Cache::remember('national_heatmap_data', now()->addMinutes(5), function () {
            $oferta = Technician::select('state', DB::raw('count(*) as total'))->groupBy('state')->pluck('total', 'state');
            $demanda = Ticket::select('state', DB::raw('count(*) as total'))->whereIn('status', ['open', 'assigned'])->groupBy('state')->pluck('total', 'state');

            $states = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
            $heatmap = [];

            foreach ($states as $state) {
                $tCount = $oferta->get($state, 0);
                $tkCount = $demanda->get($state, 0);
                
                $heatmap[] = [
                    'state' => $state,
                    'technicians_count' => $tCount,
                    'active_tickets_count' => $tkCount,
                    'opportunity_index' => ($tkCount > ($tCount * 3)) ? 'ALTA DEMANDA' : (($tCount === 0 && $tkCount > 0) ? 'CRÍTICO' : 'ESTÁVEL')
                ];
            }
            return $heatmap;
        });

        return response()->json($data);
    }
}