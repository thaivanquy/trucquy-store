<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(base_path('routes/admin.php'));
Route::prefix('shop')->group(base_path('routes/shop.php'));
