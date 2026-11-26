<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages;

use App\Filament\App\Pages\Dashboard;
use App\Models\User;
use App\Settings\OrganizationSettings;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class DashboardHeadingTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('app');
    }

    public function test_dashboard_is_titled_with_the_organization_and_greets_the_member(): void
    {
        OrganizationSettings::fake(['name' => '3rd Battalion']);

        $this->actingAs(User::factory()->createQuietly(['name' => 'Jordan Reyes']));

        Livewire::test(Dashboard::class)
            ->assertSee('3rd Battalion')
            ->assertSee('Welcome back, Jordan.')
            ->assertDontSee('results-driven organizations');
    }
}
