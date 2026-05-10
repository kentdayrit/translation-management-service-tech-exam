<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TranslationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::middleware('throttle:api')->group(function () {
        Route::get('/translations/search', [TranslationController::class, 'index']);
        Route::get('/translations/export/{locale}', [TranslationController::class, 'export']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/translations', [TranslationController::class, 'store']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });
});