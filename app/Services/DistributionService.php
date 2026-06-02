namespace App\Services;

use App\Models\Technician;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DistributionService
{
    /**
     * Algoritmo purificado para rodar direto nos índices espaciais do MySQL 8.
     */
    public function findNearbyTechniciansForTicket(Ticket $ticket, int $categoryId, float $radiusInKm = 20.0): Collection
    {
        $ticketLocation = DB::table('tickets')->where('id', $ticket->id)->value('location');

        if (!$ticketLocation) return collect();

        return Technician::with(['user'])
            ->whereHas('user', function($q) { $q->where('is_active', true); })
            ->whereHas('categories', function($q) use ($categoryId) { $q->where('categories.id', $categoryId); })
            ->whereRaw("ST_Distance_Sphere(location, ?) <= ?", [$ticketLocation, ($radiusInKm * 1000)])
            ->select('*')
            ->selectRaw("ST_Distance_Sphere(location, ?) / 1000 AS distance_km", [$ticketLocation])
            ->orderBy('distance_km')
            ->get();
    }
}