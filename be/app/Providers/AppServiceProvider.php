<?php

namespace App\Providers;

use App\Services\Admin\AuthService as AdminAuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AdminAuthService::class, function () {
            return AdminAuthService::getInstance();
        });
    }

    public function boot(): void
    {
    }
}
