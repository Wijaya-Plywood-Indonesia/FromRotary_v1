<?php

namespace App\Providers\Filament;

use App\Filament\Resources\JenisKayus\JenisKayuResource;
use App\Filament\Resources\KategoriMesins\KategoriMesinResource;
use App\Filament\Resources\Lahans\LahanResource;
use App\Filament\Resources\Mesins\MesinResource;
use App\Filament\Resources\Pegawais\PegawaiResource;
use App\Filament\Resources\Ukurans\UkuranResource;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            // ->navigation(function () {
            //     return [
            //         NavigationGroup::make()
            //             ->label('Data Master')
            //             ->icon('heroicon-o-rectangle-stack')
            //             ->collapsible(false)
            //             ->items([
            //                 PegawaiResource::getNavigationItem(),
            //                 MesinResource::getNavigationItem(),
            //                 KategoriMesinResource::getNavigationItem(),
            //                 JenisKayuResource::getNavigationItem(),
            //                 UkuranResource::getNavigationItem(),
            //                 LahanResource::getNavigationItem(),
            //             ]),
            //     ];
            // })
        ;

    }
}
