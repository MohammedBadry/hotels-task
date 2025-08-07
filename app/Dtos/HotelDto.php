<?php

namespace App\Dtos;

class HotelDto
{
    public string $name;
    public string $location;
    public float $price_per_night;
    public float $rating;
    public string $supplier;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->location = $data['location'];
        $this->price_per_night = (float) $data['price_per_night'];
        $this->rating = (float) ($data['rating'] ?? 0);
        $this->supplier = $data['supplier'] ?? 'unknown';
    }

    public function getUniqueKey(): string
    {
        return strtolower(trim($this->name . '|' . $this->location));
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'location' => $this->location,
            'price_per_night' => $this->price_per_night,
            'rating' => $this->rating,
            'supplier' => $this->supplier,
        ];
    }
}
