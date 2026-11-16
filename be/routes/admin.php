<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Middleware\Admin\AuthenticateAdminToken;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(AuthenticateAdminToken::class)->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware(AuthenticateAdminToken::class)->group(function () {
    Route::get('categories/stats', [CategoryController::class, 'stats']);
    Route::patch('categories/reorder', [CategoryController::class, 'reorder']);
    Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus']);
    Route::apiResource('categories', CategoryController::class);
});
