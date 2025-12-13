<?php

namespace App\Providers;

use App\Models\ModalSanding;
use App\Observers\ModalSandingObserver;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use App\Models\RencanaKerjaHp;
use App\Models\PlatformHasilHp;
use App\Models\TriplekHasilHp;
use App\Observers\RencanaKerjaHpObserver;
use App\Observers\PlatformHasilHpObserver;
use App\Observers\TriplekHasilHpObserver;

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
        RencanaKerjaHp::observe(RencanaKerjaHpObserver::class);
        // PlatformHasilHp::observe(PlatformHasilHpObserver::class);
        // TriplekHasilHp::observe(TriplekHasilHpObserver::class);
    }
}
