<?php

namespace App\Providers;

use App\Models\ModalSanding;
use App\Observers\ModalSandingObserver;
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
        ModalSanding::observe(ModalSandingObserver::class);
    }
}
