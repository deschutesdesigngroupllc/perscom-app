<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Models;

use App\Models\Position;
use App\Models\Rank;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use Tests\Feature\Tenant\TenantTestCase;

class UserTest extends TenantTestCase
{
    public function test_display_name_defaults_to_name(): void
    {
        DashboardSettings::fake([
            'display_name_format' => '{name}',
        ]);

        SettingsService::flush(DashboardSettings::class);

        $user = User::factory()->create(['name' => 'John Doe']);

        $this->assertEquals('John Doe', $user->display_name);
    }

    public function test_display_name_with_rank_abbreviation_and_position(): void
    {
        DashboardSettings::fake([
            'display_name_format' => '{rank.abbreviation} {name} - {position.name}',
        ]);

        SettingsService::flush(DashboardSettings::class);

        $rank = Rank::factory()->create(['abbreviation' => 'SGT']);
        $position = Position::factory()->create(['name' => 'Fireteam Member']);

        $user = User::factory()->create([
            'name' => 'John Doe',
            'rank_id' => $rank->id,
            'position_id' => $position->id,
        ]);

        $this->assertEquals('SGT John Doe - Fireteam Member', $user->display_name);
    }

    public function test_display_name_cleans_up_when_rank_is_null(): void
    {
        DashboardSettings::fake([
            'display_name_format' => '{rank.abbreviation} {name} - {position.name}',
        ]);

        SettingsService::flush(DashboardSettings::class);

        $position = Position::factory()->create(['name' => 'Rifleman']);

        $user = User::factory()->unassigned()->create([
            'name' => 'Jane Doe',
            'position_id' => $position->id,
        ]);

        $this->assertEquals('Jane Doe - Rifleman', $user->display_name);
    }

    public function test_display_name_cleans_up_when_position_is_null(): void
    {
        DashboardSettings::fake([
            'display_name_format' => '{rank.abbreviation} {name} - {position.name}',
        ]);

        SettingsService::flush(DashboardSettings::class);

        $rank = Rank::factory()->create(['abbreviation' => 'CPL']);

        $user = User::factory()->unassigned()->create([
            'name' => 'Jane Doe',
            'rank_id' => $rank->id,
        ]);

        $this->assertEquals('CPL Jane Doe', $user->display_name);
    }

    public function test_display_name_cleans_up_when_all_relations_are_null(): void
    {
        DashboardSettings::fake([
            'display_name_format' => '{rank.abbreviation} {name} - {position.name}',
        ]);

        SettingsService::flush(DashboardSettings::class);

        $user = User::factory()->unassigned()->create([
            'name' => 'Jane Doe',
        ]);

        $this->assertEquals('Jane Doe', $user->display_name);
    }

    public function test_display_name_with_all_tokens(): void
    {
        DashboardSettings::fake([
            'display_name_format' => '{rank.abbreviation} {name} - {position.name} ({specialty.abbreviation}) [{unit.name}] {status.name}',
        ]);

        SettingsService::flush(DashboardSettings::class);

        $rank = Rank::factory()->create(['abbreviation' => 'PFC']);
        $position = Position::factory()->create(['name' => 'Grenadier']);
        $specialty = Specialty::factory()->create(['abbreviation' => '11B']);
        $unit = Unit::factory()->create(['name' => 'Alpha Company']);
        $status = Status::factory()->create(['name' => 'Active']);

        $user = User::factory()->create([
            'name' => 'John Doe',
            'rank_id' => $rank->id,
            'position_id' => $position->id,
            'specialty_id' => $specialty->id,
            'unit_id' => $unit->id,
            'status_id' => $status->id,
        ]);

        $this->assertEquals('PFC John Doe - Grenadier (11B) [Alpha Company] Active', $user->display_name);
    }

    public function test_display_name_falls_back_to_name_when_format_is_empty(): void
    {
        DashboardSettings::fake([
            'display_name_format' => '',
        ]);

        SettingsService::flush(DashboardSettings::class);

        $user = User::factory()->create(['name' => 'John Doe']);

        $this->assertEquals('John Doe', $user->display_name);
    }

    public function test_display_name_is_included_in_serialization(): void
    {
        DashboardSettings::fake([
            'display_name_format' => '{name}',
        ]);

        SettingsService::flush(DashboardSettings::class);

        $user = User::factory()->create(['name' => 'John Doe']);

        $this->assertArrayHasKey('display_name', $user->toArray());
        $this->assertEquals('John Doe', $user->toArray()['display_name']);
    }
}
