<?php

namespace App\Services;

use App\Dtos\HotelDto;
use App\Services\Amadius\SupplierAmadeusService;
use App\Services\Hotelbeds\SupplierHotelbedsService;
use App\Services\TripAdvisor\SupplierTripAdvisorService;
use App\Services\Booking\SupplierBookingService;

class HotelAggregator
{
    protected array $suppliers;

    public function __construct()
    {
        $this->suppliers = [
            app(SupplierAmadeusService::class), 
            app(SupplierHotelbedsService::class), 
            app(SupplierTripAdvisorService::class), 
            app(SupplierBookingService::class), //not woking due to remote party problem
        ];
    }

    public function search(array $params): array
    {
        $results = [];

        foreach ($this->suppliers as $supplier) {
            $hotels = $supplier->search($params);
            foreach ($hotels as $hotel) {
                $dto = new HotelDto($hotel);
                $key = $dto->getUniqueKey();

                if (!isset($results[$key]) || $dto->price < $results[$key]->price) {
                    $results[$key] = $dto;
                }
            }
        }

        // Apply filters
        $filtered = collect($results)->filter(function (HotelDto $hotel) use ($params) {
            if (!empty($params['min_price']) && $hotel->price < $params['min_price']) return false;
            if (!empty($params['max_price']) && $hotel->price > $params['max_price']) return false;
            //if (!empty($params['location']) && stripos($hotel->location, $params['location']) === false) return false;
            return true;
        });

        // Optional sorting
        if (!empty($params['sort_by'])) {
            $filtered = $filtered->sortBy($params['sort_by']);
        }

        return $filtered->map->toArray()->values()->all();
    }
}
