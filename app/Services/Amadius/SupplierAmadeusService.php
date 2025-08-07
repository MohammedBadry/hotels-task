<?php

namespace App\Services\Amadius;

use  App\Interfaces\SupplierInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Dtos\HotelDto;

class SupplierAmadeusService implements SupplierInterface
{

    protected string $baseUrl = 'https://test.api.amadeus.com';
    protected AmadeusAuthService $authService;

    public function __construct(AmadeusAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function search(array $params): array
    {
        $token = $this->authService->getAccessToken();
        if (!$token) return [];

        try {
            $cityCode = $this->mapToCityCode($params['location']);
            if (!$cityCode) {
                Log::warning("Amadeus: City mapping failed for {$params['location']}");
                return [];
            }

            // 1. Get hotel IDs for the city
            $hotels = $this->getHotels($cityCode);

            if (empty($hotels)) return [];

            // 2. Get offers for these hotels
            return $this->getHotelOffers($hotels, $params);

        } catch (\Exception $e) {
            Log::error('Amadeus Supplier Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getHotels(string $cityCode): array
    {
        $token = $this->authService->getAccessToken();

        if (!$token) return [];

        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/v1/reference-data/locations/hotels/by-city", [
                'cityCode' => $cityCode
            ]);

        if (!$response->ok()) {
            Log::error('Amadeus getHotels failed', ['response' => $response->body()]);
            return [];
        }

        return collect($response->json()['data'] ?? [])
        ->map(function ($hotel) {
            return [
                'hotelId' => $hotel['hotelId'] ?? null,
                'cityName' => $hotel['address']['cityName'] ?? 'Unknown city',
                'country' => $hotel['address']['lines'][0] ?? 'Unknown Country',
            ];
        })
        ->filter(fn ($hotel) => !empty($hotel['hotelId']))
        ->unique('hotelId')
        ->take(20)
        ->values()
        ->toArray();
    }

    private function getHotelOffers(array $hotels, array $params): array
    {
        $token = $this->authService->getAccessToken();

        if (!$token) return [];

        $hotelIds = collect($hotels)->pluck('hotelId')->implode(',');
        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/v3/shopping/hotel-offers", [
                'hotelIds' => $hotelIds,
                'adults' => $params['guests'] ?? 1,
                'checkInDate' => $params['check_in'],
                'checkOutDate' => $params['check_out'],
                'roomQuantity' => 1,
                'currency' => 'USD',
            ]);

        if (!$response->ok()) {
            Log::error('Amadeus getHotelOffers failed', ['response' => $response->body()]);
            return [];
        }

        $addressMap = collect($hotels)->keyBy('hotelId');

        return collect($response->json()['data'] ?? [])->map(function ($item) use ($addressMap) {
            $hotelId = $item['hotel']['hotelId'] ?? null;

            return [
                'name' => $item['hotel']['name'] ?? 'Unknown Hotel',
                'location' => $addressMap[$hotelId]['cityName'] . ", " .$addressMap[$hotelId]['country'],
                'price_per_night' => $item['offers'][0]['price']['total'] ?? 0,
                'rating' => $item['hotel']['rating'] ?? 0,
                'address' => $addressMap[$hotelId]['address'] ?? 'Unknown',
                'supplier' => 'amadeus',
            ];
        })->toArray();
    }

    private function mapToCityCode(string $cityName): ?string
    {
        $map = config('amadius.city_codes');
        return $map[strtolower($cityName)] ?? null;
    }
}
