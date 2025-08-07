<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Services\Hotel\Amadius\SupplierAmadeusService;

Route::get('/', function () {
    return view('welcome');
});

