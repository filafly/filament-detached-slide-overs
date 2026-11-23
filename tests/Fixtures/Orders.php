<?php

namespace Filafly\DetachedSlideOvers\Tests\Fixtures;

use Filament\Actions\Action;
use Filament\Pages\Page;

class Orders extends Page
{
    protected static ?string $slug = 'orders';

    protected string $view = 'filament-panels::pages.page';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->detached()
                ->modalHeading('Edit order')
                ->action(fn () => null),
            Action::make('notes')
                ->slideOver()
                ->detached(false)
                ->modalHeading('Order notes')
                ->action(fn () => null),
            Action::make('preview')
                ->slideOver()
                ->modalHeading('Preview order')
                ->action(fn () => null),
        ];
    }
}
