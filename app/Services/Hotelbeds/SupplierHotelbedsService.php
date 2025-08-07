<?php

namespace App\Services\Hotelbeds;

use App\Interfaces\SupplierInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupplierHotelbedsService implements SupplierInterface
{
    protected string $apiKey;
    protected string $secret;
    protected string $baseUrl = 'https://api.test.hotelbeds.com';

    public function __construct()
    {
        $this->apiKey = env('HOTELBEDS_API_KEY');
        $this->secret = env('HOTELBEDS_API_SECRET');
    }

    public function search(array $params): array
    {
        try {

            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'X-Signature' => $this->generateSignature(),
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/hotel-content-api/1.0/hotels", [
                'destinationCode' => $this->getCityDestId($params['location']),
                'checkIn' => $params['check_in'],
                'checkOut' => $params['check_out'],
            ]);

            if (!$response->ok()) {
                Log::error('Hotelbeds request failed', ['response' => $response->body()]);
                return [];
            }

            return collect($response->json()['hotels'] ?? [])->map(function ($hotel) {
                return [
                    'name' => $hotel['name']['content'],
                    'location' => $hotel['city']['content'],
                    'price_per_night' => $hotel['amount'] ?? 0,
                    'rating' => $hotel['ranking'] ?? 'N/A',
                    'supplier' => 'hotelbeds',
                ];
            })->toArray();

        } catch (\Exception $e) {
            Log::error('Hotelbeds exception: ' . $e->getMessage());
            return [];
        }
    }

    protected function getCityDestId(string $city): ?string
    {
        $response = Http::withHeaders([
            'Api-Key' => env('HOTELBEDS_API_KEY'),
            'X-Signature' => $this->generateSignature(),
            'Accept' => 'application/json',
        ])->get("{$this->baseUrl}/hotel-content-api/1.0/locations/destinations/", [
            'countryCodes' => 'EG,US,FR,UAE',
            'locale' => 'en-us',
        ]);

        $destinations = $response->json()['destinations'] ?? [];

        $match = collect($destinations)->first(function ($destination) use ($city) {
            return str_contains(strtolower($destination['name']['content']), strtolower($city));
        });
        $destinationCode = $match['code'] ?? null;

        return $destinationCode ?? null;
    }    

    private function generateSignature(): string
    {
        $timestamp = time();
        return hash('sha256', $this->apiKey . $this->secret . $timestamp);
    }
}
