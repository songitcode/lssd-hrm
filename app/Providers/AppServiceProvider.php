<?php

namespace App\Providers;

use App\Models\Announcement;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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

        Gate::define('view-analytics', fn ($user) => $user->isManager());
        Gate::define('manage-announcements', fn ($user) => in_array(
            $user->role,
            config('roles.high_permission_roles', []),
            true
        ));

        View::composer('partials.announcement_bell', function ($view) {
            if (! auth()->check()) {
                $view->with('unreadAnnouncements', collect())
                    ->with('unreadAnnouncementCount', 0);

                return;
            }

            $userId = auth()->id();
            $unread = Announcement::query()
                ->whereDoesntHave('readByUsers', fn ($query) => $query->whereKey($userId));

            $view->with('unreadAnnouncements', (clone $unread)
                ->with('author.employee')
                ->latest()
                ->limit(8)
                ->get())
                ->with('unreadAnnouncementCount', $unread->count());
        });

    }
}
