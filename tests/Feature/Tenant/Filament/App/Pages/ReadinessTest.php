<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages;

use App\Filament\App\Pages\Readiness;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\Unit;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class ReadinessTest extends TenantTestCase
{
    public function test_page_shows_unit_summaries_and_expiring_items(): void
    {
        $unit = Unit::factory()->createQuietly(['name' => 'Alpha Company']);
        $member = User::factory()->createQuietly(['name' => 'Jane Doe', 'unit_id' => $unit->getKey()]);

        QualificationRecord::factory()->createQuietly([
            'user_id' => $member->getKey(),
            'qualification_id' => Qualification::factory()->createQuietly(['name' => 'Airborne'])->getKey(),
            'expires_at' => now()->addDays(7),
        ]);

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);

        Livewire::test(Readiness::class)
            ->assertSuccessful()
            ->assertSee('Alpha Company')
            ->assertSee('Airborne')
            ->assertSee('Jane Doe');
    }

    public function test_members_without_record_permissions_cannot_access_the_page(): void
    {
        $this->actingAs(User::factory()->createQuietly());

        $this->assertFalse(Readiness::canAccess());

        Livewire::test(Readiness::class)->assertForbidden();
    }
}
