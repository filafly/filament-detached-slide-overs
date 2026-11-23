<?php

namespace Filafly\DetachedSlideOvers;

use Filament\Actions\Action;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DetachedSlideOversServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filafly-detached-slide-overs');
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('detached-slide-overs', __DIR__.'/../resources/css/detached-slide-overs.css'),
        ], 'filafly/filament-detached-slide-overs');

        /**
         * Open the action in a slide-over that floats off the edge of the
         * screen. Pass `false` to keep one action's slide-over attached to the
         * edge when the plugin detaches them everywhere.
         */
        Action::macro('detached', function (bool $condition = true): Action {
            /** @var Action $this */
            if ($condition) {
                $this->slideOver();
            }

            return $this->extraModalWindowAttributes([
                'class' => $condition ? DetachedSlideOvers::DETACHED_CLASS : DetachedSlideOvers::ATTACHED_CLASS,
            ], merge: true);
        });
    }
}
