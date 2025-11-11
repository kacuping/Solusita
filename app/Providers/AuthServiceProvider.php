<?php

namespace App\Providers;

use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Intersepsi semua ability secara dinamis.
        Gate::before(function (User $user, string $ability) {
            $service = app(PermissionService::class);
            return $service->allowed($user, $ability) ? true : null;
        });
    }
}

