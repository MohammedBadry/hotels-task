<?php

namespace App\Services\Booking;

use App\Interfaces\SupplierInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupplierBookingService implements SupplierInterface
{
    protected string $baseUrl = 'https://booking-com.p.rapidapi.com/v1';

    public function search(array $params): array
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => env('BOOKING_API_KEY'),
                'X-RapidAPI-Host' => 'booking-com.p.rapidapi.com',
            ])->get("{$this->baseUrl}/hotels/search", [
                'dest_type' => 'city',
                'locale' => 'en-us',
                'units' => 'metric',
                'order_by' => 'price',
                'checkin_date' => $params['check_in'],
                'checkout_date' => $params['check_out'],
                'adults_number' => $params['guests'] ?? 1,
                'dest_id' => $this->getCityDestId($params['location']),
                'room_number' => 1,
            ]);
            if (!$response->ok()) {
                Log::error('Booking API failed', ['body' => $response->body()]);
                return [];
            }

            return collect($response->json()['result'] ?? [])->map(function ($hotel) {
                return [
                    'name' => $hotel['hotel_name'] ?? '',
                    'location' => $hotel['city'] ?? '',
                    'price_per_night' => $hotel['min_total_price'] ?? 0,
                    'rating' => $hotel['review_score'] ?? 0,
                    'supplier' => 'booking',
                ];
            })->toArray();

        } catch (\Exception $e) {
            Log::error('Booking integration error: ' . $e->getMessage());
            return [];
        }
    }

    protected function getCityDestId(string $city): ?string
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Key' => env('BOOKING_API_KEY'),
            'X-RapidAPI-Host' => 'booking-com.p.rapidapi.com',
        ])->get("{$this->baseUrl}/hotels/locations", [
            'name' => $city,
            'locale' => 'en-us',
        ]);

        return $response->json()[0]['dest_id'] ?? null;
    }
}
