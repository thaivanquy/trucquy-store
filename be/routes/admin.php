<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\CommonDataController;
use App\Http\Controllers\Api\Admin\SupplierController;
use App\Http\Controllers\Api\Admin\UnitController;
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

    Route::patch('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus']);
    Route::apiResource('suppliers', SupplierController::class);

    Route::get('units/stats', [UnitController::class, 'stats']);
    Route::patch('units/reorder', [UnitController::class, 'reorder']);
    Route::patch('units/{unit}/toggle-status', [UnitController::class, 'toggleStatus']);
    Route::apiResource('units', UnitController::class);

    Route::get('provinces', [CommonDataController::class, 'getProvinces']);
    Route::get('provinces/{province}/wards', [CommonDataController::class, 'getWards']);
    Route::get('banks', [CommonDataController::class, 'getBanks']);
});
