<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App;

use Filament\Facades\Filament;
use Tests\Feature\Tenant\TenantTestCase;

class DarkModeTest extends TenantTestCase
{
    public function test_every_panel_is_dark_only(): void
    {
        foreach (['app', 'admin', 'public'] as $panel) {
            $this->assertTrue(Filament::getPanel($panel)->hasDarkModeForced(), "The {$panel} panel should force dark mode.");
        }
    }
}
