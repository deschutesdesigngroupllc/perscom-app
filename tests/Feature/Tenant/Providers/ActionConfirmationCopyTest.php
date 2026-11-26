<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Providers;

use App\Filament\App\Resources\RankResource\Pages\ListRanks;
use App\Models\Rank;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class ActionConfirmationCopyTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_delete_confirmation_names_the_action_and_its_consequence(): void
    {
        $rank = Rank::factory()->createQuietly();

        Livewire::test(ListRanks::class)
            ->mountAction(TestAction::make('delete')->table($rank))
            ->assertMountedActionModalSee('Delete Rank')
            ->assertMountedActionModalSee('This permanently removes it and its history.')
            ->assertMountedActionModalSee('Keep')
            ->assertMountedActionModalDontSee('Are you sure');
    }

    public function test_bulk_delete_confirmation_names_the_selection(): void
    {
        $ranks = Rank::factory()->count(2)->createQuietly();

        Livewire::test(ListRanks::class)
            ->selectTableRecords($ranks)
            ->mountAction(TestAction::make('delete')->table()->bulk())
            ->assertMountedActionModalSee('Delete Selected Ranks')
            ->assertMountedActionModalDontSee('Are you sure');
    }
}
