<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAdvertisementController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/advertisements', [ApiAdvertisementController::class, 'index']);
    Route::get('/advertisements/{id}', [ApiAdvertisementController::class, 'show']);
    Route::post('/advertisements', [ApiAdvertisementController::class, 'store']);
    Route::put('/advertisements/{id}', [ApiAdvertisementController::class, 'update']);
    Route::delete('/advertisements/{id}', [ApiAdvertisementController::class, 'destroy']);
});