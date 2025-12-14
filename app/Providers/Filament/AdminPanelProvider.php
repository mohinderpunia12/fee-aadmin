<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureUserIsSuperAdmin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandLogo(asset('logo.png'))
            ->brandLogoHeight('2.5rem')
            ->darkMode(false)
            ->colors([
                'primary' => [
                    50 => '#e8e9f5',
                    100 => '#d1d3eb',
                    200 => '#a3a7d7',
                    300 => '#757bc3',
                    400 => '#474faf',
                    500 => '#2D32B8',
                    600 => '#2529a0',
                    700 => '#1d2088',
                    800 => '#151770',
                    900 => '#0d0e58',
                    950 => '#060740',
                ],
                'success' => [
                    50 => '#e6faf7',
                    100 => '#ccf5ef',
                    200 => '#99ebdf',
                    300 => '#66e1cf',
                    400 => '#33d7bf',
                    500 => '#00C29E',
                    600 => '#009b7e',
                    700 => '#00745e',
                    800 => '#004d3f',
                    900 => '#00261f',
                    950 => '#001310',
                ],
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                \App\Filament\Admin\Widgets\AdminStatsWidget::class,
                Widgets\AccountWidget::class,
            ])
            ->authGuard('web')
            ->databaseNotifications()
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
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsSuperAdmin::class,
            ]);
    }
}
