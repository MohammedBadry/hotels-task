<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelSearchController;


Route::get('/hotels/search', [HotelSearchController::class, 'search']);
