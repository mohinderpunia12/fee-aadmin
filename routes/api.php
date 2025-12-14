<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    // Rate limit registration and login to prevent brute force attacks
    Route::post('register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1'); // 5 attempts per minute
    
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1'); // 5 attempts per minute

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
    });
});
