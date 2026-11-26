<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\View;

use Tests\Feature\Tenant\TenantTestCase;

class WidgetLayoutTest extends TenantTestCase
{
    public function test_dark_false_renders_the_light_theme(): void
    {
        $this->withoutVite();
        request()->query->set('dark', 'false');

        $this->blade('<x-layouts.widget>Content</x-layouts.widget>')
            ->assertDontSee('class="dark', false)
            ->assertDontSee(' dark"', false);
    }

    public function test_dark_true_renders_the_dark_theme(): void
    {
        $this->withoutVite();
        request()->query->set('dark', 'true');

        $this->blade('<x-layouts.widget>Content</x-layouts.widget>')
            ->assertSee('antialiased dark', false);
    }

    public function test_no_dark_parameter_renders_the_light_theme(): void
    {
        $this->withoutVite();
        request()->query->remove('dark');

        $this->blade('<x-layouts.widget>Content</x-layouts.widget>')
            ->assertDontSee('antialiased dark', false);
    }
}
