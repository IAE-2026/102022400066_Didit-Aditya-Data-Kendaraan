<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;
use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware([ApiAuthMiddleware::class])->group(function () {
    Route::get('/v1/vehicles', [VehicleController::class, 'index']);
    Route::get('/v1/vehicles/{id}', [VehicleController::class, 'show']);
    Route::post('/v1/vehicles', [VehicleController::class, 'store']);
});
