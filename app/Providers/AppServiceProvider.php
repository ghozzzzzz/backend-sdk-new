<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Gate;
use App\Models\Pemesanan;
use App\Policies\PemesananPolicy;
use App\Models\Komunitas;
use App\Policies\KomunitasPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policies mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Pemesanan::class => PemesananPolicy::class,
        Komunitas::class => KomunitasPolicy::class,
    ];

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
        $this->configureRateLimiting();

        // Registrasi gate untuk admin
        Gate::define('admin', function ($user) {
            return $user instanceof \App\Models\User && $user->role === 'admin';
        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}