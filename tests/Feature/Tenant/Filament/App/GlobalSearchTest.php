<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App;

use App\Models\Newsfeed;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Livewire\GlobalSearch;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class GlobalSearchTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);

        Filament::setCurrentPanel(Filament::getPanel('app'));
    }

    public function test_global_search_runs_against_every_resource(): void
    {
        Livewire::test(GlobalSearch::class)
            ->set('search', 'test')
            ->assertOk();
    }

    public function test_newsfeed_items_are_found_by_headline(): void
    {
        Newsfeed::factory()->createQuietly(['properties' => ['headline' => 'Exercise Robin Sage Begins', 'text' => 'Details']]);

        Livewire::test(GlobalSearch::class)
            ->set('search', 'Robin Sage')
            ->assertOk()
            ->assertSee('Exercise Robin Sage Begins');
    }
}
