namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleMapsService
{
    public function getCoordinatesFromAddress(string $address): ?array
    {
        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => config('services.google.maps_api_key'),
                'region' => 'br'
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                return [
                    'lat' => $response->json('results.0.geometry.location.lat'),
                    'lng' => $response->json('results.0.geometry.location.lng')
                ];
            }
            return null;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }
}