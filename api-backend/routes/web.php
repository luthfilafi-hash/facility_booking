<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacilityController;

Route::get('/', function () {
    return view('welcome');
});

// Our new visual page route
Route::get('/facilities', [FacilityController::class, 'index']);
