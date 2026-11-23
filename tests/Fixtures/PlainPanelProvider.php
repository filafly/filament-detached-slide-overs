<?php

namespace Filafly\DetachedSlideOvers\Tests\Fixtures;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * A panel without the plugin: `detached()` still works on its own.
 */
class PlainPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('plain')
            ->path('plain')
            ->pages([
                Dashboard::class,
            ])
            ->middleware([
                EncryptCookies::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
