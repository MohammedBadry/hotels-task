<?php

namespace App\Services\TripAdvisor;

use App\Interfaces\SupplierInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupplierTripAdvisorService implements SupplierInterface
{
    protected string $baseUrl = 'https://booking-com18.p.rapidapi.com';

    public function search(array $params): array
    {
        try {
            $locationId = $this->getLocationId($params['location']);

            if (!$locationId) return [];
            
                $response = Http::withHeaders([
                'X-RapidAPI-Host' => 'booking-com18.p.rapidapi.com',
                'X-RapidAPI-Key' => env('TRIPADVISOR_API_KEY'),
                ])->get("{$this->baseUrl}/stays/search", [
                    'locationId' => $locationId,
                    'units' => 'metric',
                    'temperature' => 'c',
                    'currency' => 'USD',
                    'checkinDate' => $params['check_in'],
                    'checkoutDate' => $params['check_out'],
                ]);

            if (!$response->ok()) {
                Log::error('TripAdvisor API failed', ['body' => $response->body()]);
                return [];
            }

            return collect($response->json()['data'] ?? [])
                ->filter(fn($item) => isset($item['name'], $item['priceBreakdown']['grossPrice']['value']))
                ->map(fn($hotel) => [
                    'name' => $hotel['name'],
                    'location' => $hotel['wishlistName'] ?? '',
                    'price_per_night' => $hotel['priceBreakdown']['grossPrice']['value'] ?? 0,
                    'rating' => $hotel['rankingPosition'] ?? 'N/A',
                    'supplier' => 'tripadvisor',
                ])
                ->toArray();

        } catch (\Exception $e) {
            Log::error('TripAdvisor integration error: ' . $e->getMessage());
            return [];
        }
    }

    protected function getLocationId(string $city): ?string
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Host' => 'booking-com18.p.rapidapi.com',
            'X-RapidAPI-Key' => env('TRIPADVISOR_API_KEY'),
        ])->get("{$this->baseUrl}/stays/auto-complete", [
            'query' => $city,
        ]);


        if (!$response->ok()) return null;

        $data = $response->json()['data'][0] ?? null;
        return $data['id'] ?? null;
    }
}
