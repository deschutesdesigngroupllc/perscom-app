<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\RankResource\Pages\EditRank;
use App\Models\Qualification;
use App\Models\Rank;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class RankRequirementTest extends TenantTestCase
{
    public function test_promotion_requirements_can_be_saved_on_a_rank(): void
    {
        $this->actingAsSuperAdmin();

        $rank = Rank::factory()->createQuietly();
        $qualification = Qualification::factory()->createQuietly();

        Livewire::test(EditRank::class, ['record' => $rank->getKey()])
            ->fillForm([
                'requirement.min_days_in_grade' => 90,
                'requirement.min_events_attended_90d' => 4,
                'requirement.required_qualification_ids' => [(string) $qualification->getKey()],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $requirement = $rank->fresh()->requirement;

        $this->assertSame(90, $requirement->min_days_in_grade);
        $this->assertSame(4, $requirement->min_events_attended_90d);
        $this->assertNull($requirement->min_days_in_service);
        $this->assertEquals([$qualification->getKey()], $requirement->required_qualification_ids);
    }

    public function test_no_requirement_is_created_when_left_blank(): void
    {
        $this->actingAsSuperAdmin();

        $rank = Rank::factory()->createQuietly();

        Livewire::test(EditRank::class, ['record' => $rank->getKey()])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull($rank->fresh()->requirement);
    }

    protected function actingAsSuperAdmin(): void
    {
        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());

        $this->actingAs($admin);
    }
}
