<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Semester;
use App\Models\TahunAkademik;
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
        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Shared data for sidebar and header
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $activeTahun = TahunAkademik::active()->first();
            $activeSemester = Semester::active()->first();
            $view->with(compact('activeTahun', 'activeSemester'));

            if (Auth::check()) {
                $unreadNotifications = Auth::user()->notifikasi()->unread()->latest()->limit(5)->get();
                $unreadCount = Auth::user()->notifikasi()->unread()->count();
                $view->with(compact('unreadNotifications', 'unreadCount'));
            }
        });
    }
}
