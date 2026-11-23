<?php

namespace Filafly\DetachedSlideOvers\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filafly\DetachedSlideOvers\DetachedSlideOversPlugin;
use Filafly\DetachedSlideOvers\DetachedSlideOversServiceProvider;
use Filafly\DetachedSlideOvers\Tests\Fixtures\AdminPanelProvider;
use Filafly\DetachedSlideOvers\Tests\Fixtures\PlainPanelProvider;
use Filafly\DetachedSlideOvers\Tests\Fixtures\User;
use Filament\Actions\ActionsServiceProvider;
use Filament\Facades\Filament;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
            ActionsServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentServiceProvider::class,
            DetachedSlideOversServiceProvider::class,
            AdminPanelProvider::class,
            PlainPanelProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('auth.providers.users.model', User::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    protected function plugin(): DetachedSlideOversPlugin
    {
        /** @var DetachedSlideOversPlugin $plugin */
        $plugin = Filament::getPanel('admin')->getPlugin('filafly-detached-slide-overs');

        return $plugin;
    }

    protected function signIn(): User
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user);

        return $user;
    }
}
