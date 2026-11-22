<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Providers;

use Filament\Support\Colors\Color;
use Tests\Feature\Tenant\TenantTestCase;

class BrandColorsTest extends TenantTestCase
{
    public function test_pages_outside_a_panel_use_the_brand_primary_color(): void
    {
        $this->get(route('app.invites.accept', ['token' => 'not-a-real-token']))
            ->assertOk()
            ->assertSee(Color::Blue[600], false)
            ->assertDontSee(Color::Amber[600], false);
    }
}
