<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
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
        //
        // FilamentAsset::register([
        //     Css::make(
        //         'custom-theme',
        //         __DIR__ . '/../../resources/css/filament/admin/theme.css'
        //     ),

        // ]);
    }
}
