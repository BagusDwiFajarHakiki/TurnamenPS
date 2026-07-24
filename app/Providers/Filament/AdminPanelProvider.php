<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
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
            ->spa()
            ->globalSearch(false)
            ->brandName('Infinity Boxzone')
            ->brandLogo(fn () => view('filament.brand-logo'))
            ->favicon('/images/logo.png')
            ->colors([
                'primary'   => Color::hex('#39D353'),  // Neon green
                'gray'      => Color::Zinc,
                'danger'    => Color::Red,
                'warning'   => Color::Amber,
                'success'   => Color::Emerald,
                'info'      => Color::Sky,
            ])
            ->font('Outfit', provider: \Filament\FontProviders\GoogleFontProvider::class)
            ->navigationGroups([
                'Turnamen',
                'Pertandingan',
                'Peserta & Pembayaran',
                'Infrastruktur',
                'Sistem',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\TournamentStatsOverview::class,
                \App\Filament\Widgets\PendingPaymentsWidget::class,
                \App\Filament\Widgets\LatestMatchesWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
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
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => '<style>
                    /* Desktop Layout: Force scrollbar to be below header */
                    @media (min-width: 1025px) {
                        html, body {
                            overflow: hidden !important;
                        }
                        /* Kontainer utama (di bawah topbar) disesuaikan tingginya */
                        .fi-layout {
                            height: calc(100vh - 64px) !important; /* Topbar Filament umumnya setinggi 64px */
                            margin-top: 64px !important;
                        }
                        /* Topbar dibuat fixed di atas agar tidak ikut scroll */
                        .fi-topbar {
                            position: fixed !important;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 64px;
                            z-index: 40;
                        }
                        /* Konten utama yang akan memunculkan scrollbar */
                        .fi-main-ctn {
                            height: 100% !important;
                            overflow-y: auto !important;
                            overflow-x: hidden !important;
                        }
                    }

                    /* Mobile Layout: Default scrolling */
                    @media (max-width: 1024px) {
                        html, body, .fi-layout, .fi-main-ctn {
                            height: auto !important;
                            overflow: visible !important;
                            overscroll-behavior-y: auto !important;
                        }
                        .fi-sidebar { position: fixed !important; }
                    }
                </style>'
            );
    }
}
