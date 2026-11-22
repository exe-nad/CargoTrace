<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiRelatedController;
use App\Http\Controllers\Api\ApiShipmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CargoTrace REST API Endpoints (Slide 12 & 13)
|--------------------------------------------------------------------------
*/

// Public Authentication
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);

// Protected API routes using Laravel Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Profile
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);

    // Shipments Resource
    Route::get('/shipments', [ApiShipmentController::class, 'index']);
    Route::post('/shipments', [ApiShipmentController::class, 'store']);
    Route::get('/shipments/{id}', [ApiShipmentController::class, 'show']);
    Route::match(['put', 'patch'], '/shipments/{id}', [ApiShipmentController::class, 'update']);
    Route::delete('/shipments/{id}', [ApiShipmentController::class, 'destroy']);

    // Shipment Tracking Endpoints
    Route::get('/shipments/{id}/tracking', [ApiShipmentController::class, 'getTracking']);
    Route::post('/shipments/{id}/tracking', [ApiShipmentController::class, 'postTracking']);

    // Carriers
    Route::get('/carriers', [ApiRelatedController::class, 'getCarriers']);
    Route::post('/carriers', [ApiRelatedController::class, 'postCarriers']);

    // Locations
    Route::get('/locations', [ApiRelatedController::class, 'getLocations']);
    Route::post('/locations', [ApiRelatedController::class, 'postLocations']);

    // Exceptions
    Route::get('/shipments/{id}/exceptions', [ApiRelatedController::class, 'getShipmentExceptions']);
    Route::post('/shipments/{id}/exceptions', [ApiRelatedController::class, 'postShipmentExceptions']);
    Route::patch('/exceptions/{id}', [ApiRelatedController::class, 'patchException']);
});
