<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Settings;

use App\Models\Group;
use App\Models\Position;
use App\Models\Rank;
use App\Models\Status;
use App\Models\Unit;
use App\Settings\OnboardingSettings;
use Tests\Feature\Tenant\TenantTestCase;

class OnboardingSettingsTest extends TenantTestCase
{
    public function test_onboarding_settings_defaults_are_correct(): void
    {
        /** @var OnboardingSettings $settings */
        $settings = app(OnboardingSettings::class);

        // Reset to defaults for testing
        $settings->completed = false;
        $settings->dismissed = false;
        $settings->completed_at = null;
        $settings->save();

        $this->assertFalse($settings->completed);
        $this->assertFalse($settings->dismissed);
        $this->assertNull($settings->completed_at);
        $this->assertTrue($settings->isAccessible());
    }

    public function test_onboarding_is_not_accessible_after_completion(): void
    {
        /** @var OnboardingSettings $settings */
        $settings = app(OnboardingSettings::class);

        $settings->markCompleted();

        $this->assertTrue($settings->completed);
        $this->assertNotNull($settings->completed_at);
        $this->assertFalse($settings->isAccessible());
    }

    public function test_onboarding_is_not_accessible_after_dismissal(): void
    {
        /** @var OnboardingSettings $settings */
        $settings = app(OnboardingSettings::class);

        $settings->markDismissed();

        $this->assertTrue($settings->dismissed);
        $this->assertFalse($settings->isAccessible());
    }

    public function test_group_can_be_created(): void
    {
        Group::create([
            'name' => 'Test Group',
            'description' => 'Test Description',
        ]);

        $this->assertDatabaseHas(Group::class, [
            'name' => 'Test Group',
            'description' => 'Test Description',
        ]);
    }

    public function test_unit_can_be_created_and_attached_to_group(): void
    {
        $group = Group::factory()->createQuietly(['name' => 'Test Group']);

        $unit = Unit::create([
            'name' => 'Test Unit',
            'description' => 'Unit Description',
        ]);

        $unit->groups()->attach($group->id);

        $this->assertDatabaseHas(Unit::class, [
            'name' => 'Test Unit',
            'description' => 'Unit Description',
        ]);

        $this->assertTrue($unit->groups->contains($group->id));
    }

    public function test_position_can_be_created(): void
    {
        Position::create([
            'name' => 'Test Position',
            'description' => 'Position Description',
        ]);

        $this->assertDatabaseHas(Position::class, [
            'name' => 'Test Position',
            'description' => 'Position Description',
        ]);
    }

    public function test_rank_can_be_created(): void
    {
        Rank::create([
            'name' => 'Test Rank',
            'abbreviation' => 'TR',
            'paygrade' => 'E-1',
        ]);

        $this->assertDatabaseHas(Rank::class, [
            'name' => 'Test Rank',
            'abbreviation' => 'TR',
            'paygrade' => 'E-1',
        ]);
    }

    public function test_status_can_be_created(): void
    {
        Status::create([
            'name' => 'Test Status',
            'color' => '#22c55e',
        ]);

        $this->assertDatabaseHas(Status::class, [
            'name' => 'Test Status',
            'color' => '#22c55e',
        ]);
    }

    public function test_completed_at_is_set_when_marking_completed(): void
    {
        /** @var OnboardingSettings $settings */
        $settings = app(OnboardingSettings::class);
        $settings->completed = false;
        $settings->completed_at = null;
        $settings->save();

        $this->assertNull($settings->completed_at);

        $settings->markCompleted();

        $this->assertNotNull($settings->completed_at);
        $this->assertTrue($settings->completed);
    }
}
