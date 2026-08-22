<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Listeners;

use App\Listeners\RegisterCustomPages;
use App\Models\Page;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Stancl\Tenancy\Events\TenancyInitialized;
use Tests\Feature\Tenant\TenantTestCase;

class RegisterCustomPagesTest extends TenantTestCase
{
    public function test_handle_registers_navigation_items_for_each_page(): void
    {
        config([
            'tenancy.enabled' => true,
            'tenancy.central_domains' => ['central.perscom.test'],
        ]);

        $page = Page::factory()->create([
            'name' => 'Registered Handbook Page',
            'icon' => 'heroicon-o-home',
        ]);

        (new RegisterCustomPages)->handle(new TenancyInitialized(tenancy()));

        $panel = Filament::getCurrentOrDefaultPanel();

        $labels = collect($panel->getNavigationItems())
            ->map(fn (NavigationItem $item): string => $item->getLabel());

        $this->assertTrue($labels->contains($page->name));
    }

    public function test_handle_returns_early_on_the_central_admin_context(): void
    {
        // App::isAdmin() is true when the current request host is a central
        // domain; forcing that makes the listener return before registering pages.
        config([
            'tenancy.enabled' => true,
            'tenancy.central_domains' => [request()->getHost()],
        ]);

        $page = Page::factory()->create([
            'name' => 'Admin Context Only Page',
            'icon' => 'heroicon-o-home',
        ]);

        (new RegisterCustomPages)->handle(new TenancyInitialized(tenancy()));

        $panel = Filament::getCurrentOrDefaultPanel();

        $labels = collect($panel->getNavigationItems())
            ->map(fn (NavigationItem $item): string => $item->getLabel());

        $this->assertFalse($labels->contains($page->name));
    }
}
