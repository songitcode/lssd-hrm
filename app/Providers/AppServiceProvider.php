<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
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
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // $this->registerPolicies();

        Paginator::useBootstrap(); // Để dùng phân trang với Bootstrap

        // Định nghĩa quyền quản lý lương
        Gate::define('manage_salary', function ($user) {
            $allowedRoles = ['admin', 'cục trưởng', 'phó cục trưởng'];
            return in_array($user->role, $allowedRoles);
        });

        Gate::define('manage-attendance', function ($user) {
            $allowedRoles = ['admin', 'cục trưởng', 'phó cục trưởng'];
            return in_array($user->role, $allowedRoles);

        });

    }
}
