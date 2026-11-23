<?php

namespace Filafly\DetachedSlideOvers;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use InvalidArgumentException;

class DetachedSlideOversPlugin implements Plugin
{
    protected bool $isEverywhere = true;

    protected ?string $gap = null;

    protected ?string $radius = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'filafly-detached-slide-overs';
    }

    public function register(Panel $panel): void
    {
        // Render hooks are registered globally once a panel boots, so each
        // hook checks that it is rendering this plugin's panel.
        $panelId = $panel->getId();

        $panel
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): ?HtmlString => $this->isCurrentPanel($panelId)
                    ? $this->renderCustomProperties($panelId)
                    : null,
            )
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): ?HtmlString => $this->isCurrentPanel($panelId) && $this->isEverywhere()
                    ? new HtmlString('<div class="'.DetachedSlideOvers::EVERYWHERE_CLASS.'" hidden></div>')
                    : null,
            );
    }

    public function boot(Panel $panel): void {}

    /**
     * Detach every slide-over in the panel: actions, table and form actions,
     * and the database notifications panel. On by default once the plugin is
     * registered. Pass `false` to detach only actions that call `detached()`.
     */
    public function everywhere(bool $condition = true): static
    {
        $this->isEverywhere = $condition;

        return $this;
    }

    /**
     * The space between a detached slide-over and the edges of the screen,
     * as a CSS length. Defaults to `0.5rem` on phones and `1rem` from the
     * `sm` breakpoint up.
     */
    public function gap(?string $gap): static
    {
        $this->gap = $this->validateLength($gap, 'gap');

        return $this;
    }

    /**
     * The corner radius of a detached slide-over, as a CSS length. Defaults
     * to `0.75rem`, the radius of Filament's centered modals.
     */
    public function radius(?string $radius): static
    {
        $this->radius = $this->validateLength($radius, 'radius');

        return $this;
    }

    public function isEverywhere(): bool
    {
        return $this->isEverywhere;
    }

    public function getGap(): ?string
    {
        return $this->gap;
    }

    public function getRadius(): ?string
    {
        return $this->radius;
    }

    protected function isCurrentPanel(string $panelId): bool
    {
        return filament()->getCurrentPanel()?->getId() === $panelId;
    }

    /**
     * Scoped to this panel's `<body>` rather than `:root`, because SPA mode
     * keeps the `<style>` tags of panels visited earlier in the `<head>`.
     */
    protected function renderCustomProperties(string $panelId): ?HtmlString
    {
        $properties = array_filter([
            '--fi-detached-slide-over-gap' => $this->getGap(),
            '--fi-detached-slide-over-radius' => $this->getRadius(),
        ], fn (?string $value): bool => $value !== null);

        if ($properties === []) {
            return null;
        }

        $declarations = collect($properties)
            ->map(fn (string $value, string $property): string => "{$property}: {$value};")
            ->implode(' ');

        $selector = '.fi-body.'.$this->escapeClassName("fi-panel-{$panelId}");

        return new HtmlString("<style>{$selector} { {$declarations} }</style>");
    }

    /**
     * Escape a class name for a CSS selector, with hex escapes so that no
     * ASCII character other than letters, digits, `_` and `-` ever reaches
     * the `<style>` tag as it is.
     */
    protected function escapeClassName(string $className): string
    {
        return preg_replace_callback(
            // Non-ASCII characters are valid in CSS identifiers as they are.
            '/[^a-zA-Z0-9_\-\x{80}-\x{10FFFF}]/u',
            fn (array $match): string => '\\'.dechex(mb_ord($match[0])).' ',
            $className,
        ) ?? preg_replace_callback(
            // Not valid UTF-8: escape byte by byte instead.
            '/[^a-zA-Z0-9_\-]/',
            fn (array $match): string => '\\'.dechex(ord($match[0])).' ',
            $className,
        );
    }

    /**
     * The value is written into a `<style>` tag, so only characters that
     * appear in CSS lengths and math functions are allowed.
     */
    protected function validateLength(?string $value, string $name): ?string
    {
        if (blank($value)) {
            return null;
        }

        $value = trim($value);

        if (! preg_match('/^[a-z0-9.%()+\-*\/, ]+$/i', $value)) {
            throw new InvalidArgumentException("The detached slide-over {$name} [{$value}] must be a CSS length, such as `1rem` or `calc(1rem + 2px)`.");
        }

        // A unitless 0 is a number inside calc(), which can't be subtracted
        // from the window's 100% width.
        return $value === '0' ? '0px' : $value;
    }
}
