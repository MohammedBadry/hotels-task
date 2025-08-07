<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HotelAggregator;
use App\Http\Requests\Api\HotelSearchRequest;

class HotelSearchController extends Controller
{
    public function search(HotelSearchRequest $request, HotelAggregator $aggregator)
    {
        $hotels = $aggregator->search($request->validated());

        return response()->json(array_values($hotels));
    }
}
