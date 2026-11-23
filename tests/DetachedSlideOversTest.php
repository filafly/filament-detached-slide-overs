<?php

namespace Filafly\DetachedSlideOvers\Tests;

use Filafly\DetachedSlideOvers\Tests\Fixtures\Orders;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Blade;
use InvalidArgumentException;
use Livewire\Livewire;

class DetachedSlideOversTest extends TestCase
{
    public function test_detached_opens_the_action_in_a_detached_slide_over(): void
    {
        $action = Action::make('edit')->detached();

        $this->assertTrue($action->isModalSlideOver());
        $this->assertSame('fi-detached-slide-over', $action->getExtraModalWindowAttributes()['class']);
    }

    public function test_detached_keeps_the_actions_own_window_attributes(): void
    {
        $action = Action::make('edit')
            ->extraModalWindowAttributes(['class' => 'orders-window', 'data-order' => '42'])
            ->detached();

        $attributes = $action->getExtraModalWindowAttributes();

        $this->assertStringContainsString('orders-window', $attributes['class']);
        $this->assertStringContainsString('fi-detached-slide-over', $attributes['class']);
        $this->assertSame('42', $attributes['data-order']);
    }

    public function test_detached_false_opts_a_slide_over_out_without_changing_it_into_a_modal(): void
    {
        $slideOver = Action::make('notes')->slideOver()->detached(false);
        $modal = Action::make('confirm')->detached(false);

        $this->assertTrue($slideOver->isModalSlideOver());
        $this->assertSame('fi-attached-slide-over', $slideOver->getExtraModalWindowAttributes()['class']);
        $this->assertFalse($modal->isModalSlideOver());
    }

    public function test_the_page_mounts_the_detached_actions_as_slide_overs(): void
    {
        $this->signIn();
        Filament::setCurrentPanel('admin');

        Livewire::test(Orders::class)
            ->assertActionExists('edit', fn (Action $action): bool => $action->isModalSlideOver()
                && str_contains($action->getExtraModalWindowAttributes()['class'], 'fi-detached-slide-over'));
    }

    public function test_filament_renders_the_class_on_the_slide_overs_window(): void
    {
        $html = $this->renderModal(Action::make('edit')->detached());

        $this->assertStringContainsString('fi-modal-slide-over', $html);
        $this->assertMatchesRegularExpression('/class="(?=[^"]*\\bfi-modal-window\\b)[^"]*\\bfi-detached-slide-over\\b/', $html);

        $html = $this->renderModal(Action::make('notes')->slideOver()->detached(false));

        $this->assertMatchesRegularExpression('/class="(?=[^"]*\\bfi-modal-window\\b)[^"]*\\bfi-attached-slide-over\\b/', $html);
        $this->assertStringNotContainsString('fi-detached-slide-over', $html);
    }

    /**
     * Render Filament's modal component the way an action's modal does, with
     * the action's slide-over setting and window attributes.
     */
    protected function renderModal(Action $action): string
    {
        return Blade::render(
            '<x-filament::modal id="test" :slide-over="$slideOver" :extra-modal-window-attribute-bag="$bag" heading="Edit order">Body</x-filament::modal>',
            [
                'slideOver' => $action->isModalSlideOver(),
                'bag' => $action->getExtraModalWindowAttributeBag(),
            ],
        );
    }

    public function test_the_stylesheet_is_registered_for_every_panel(): void
    {
        $styles = collect(FilamentAsset::getStyles())
            ->map(fn ($style): string => $style->getId());

        $this->assertContains('detached-slide-overs', $styles);

        $this->signIn();

        $this->get('/plain')
            ->assertOk()
            ->assertSee('filafly/filament-detached-slide-overs/detached-slide-overs.css', escape: false);
    }

    public function test_the_plugin_detaches_every_slide_over_in_its_panel(): void
    {
        $this->signIn();

        $this->get('/admin')
            ->assertOk()
            ->assertSee('<div class="fi-detached-slide-overs" hidden></div>', escape: false);

        $this->get('/plain')
            ->assertOk()
            ->assertDontSee('<div class="fi-detached-slide-overs"', escape: false);
    }

    public function test_everywhere_false_leaves_slide_overs_alone_unless_an_action_is_detached(): void
    {
        $this->plugin()->everywhere(false);
        $this->signIn();

        $this->get('/admin')
            ->assertOk()
            ->assertDontSee('<div class="fi-detached-slide-overs"', escape: false);
    }

    public function test_gap_and_radius_are_written_as_custom_properties(): void
    {
        $this->signIn();

        $this->get('/admin')
            ->assertOk()
            ->assertDontSee('--fi-detached-slide-over-gap:', escape: false);

        $this->plugin()->gap('1.5rem')->radius('calc(1rem + 2px)');

        $this->get('/admin')
            ->assertOk()
            ->assertSee('<style>:root { --fi-detached-slide-over-gap: 1.5rem; --fi-detached-slide-over-radius: calc(1rem + 2px); }</style>', escape: false);
    }

    public function test_a_gap_that_is_not_a_css_length_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->plugin()->gap('1rem; } body { display: none');
    }

    public function test_a_radius_cannot_close_the_style_tag(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->plugin()->radius('1rem</style><script>');
    }
}
