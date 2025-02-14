<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ResourceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Resource routes
Route::apiResource('resources', ResourceController::class);

// Booking routes
Route::apiResource('bookings', BookingController::class);

// Get all bookings for a specific resource
Route::get('resources/{resource}/bookings', [BookingController::class, 'resourceBookings'])
    ->name('resources.bookings');
