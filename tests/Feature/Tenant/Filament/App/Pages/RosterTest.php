<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages;

use App\Filament\App\Pages\Roster;
use App\Models\Enums\RosterMode;
use App\Models\Group;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\Rank;
use App\Models\RankRequirement;
use App\Models\Slot;
use App\Models\Unit;
use App\Models\User;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class RosterTest extends TenantTestCase
{
    protected Group $group;

    protected Unit $company;

    protected Unit $platoon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->group = Group::factory()->createQuietly(['name' => 'First Battalion']);
        $this->company = Unit::factory()->createQuietly(['name' => 'Alpha Company']);
        $this->platoon = Unit::factory()->createQuietly(['name' => 'First Platoon', 'parent_id' => $this->company->getKey()]);
        $this->group->units()->attach([$this->company->getKey(), $this->platoon->getKey()]);

        $admin = User::factory()->createQuietly(['unit_id' => null]);
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_it_renders_nested_units_with_their_members(): void
    {
        User::factory()->createQuietly(['name' => 'Company Commander', 'unit_id' => $this->company->getKey()]);
        User::factory()->createQuietly(['name' => 'Platoon Leader', 'unit_id' => $this->platoon->getKey()]);

        Livewire::test(Roster::class)
            ->assertOk()
            ->assertSeeInOrder(['Alpha Company', 'Company Commander', 'First Platoon', 'Platoon Leader']);
    }

    public function test_search_only_shows_matching_members(): void
    {
        User::factory()->createQuietly(['name' => 'Jane Matching', 'unit_id' => $this->company->getKey()]);
        User::factory()->createQuietly(['name' => 'John Other', 'unit_id' => $this->platoon->getKey()]);

        Livewire::test(Roster::class)
            ->set('search', 'matching')
            ->assertSee('Jane Matching')
            ->assertDontSee('John Other')
            ->assertViewHas('tree', fn (array $tree): bool => count($tree) === 1
                && $tree[0]['unit']->is($this->company)
                && $tree[0]['children'] === []);
    }

    public function test_search_matches_rank_names(): void
    {
        $rank = Rank::factory()->createQuietly(['name' => 'Staff Sergeant']);
        User::factory()->createQuietly(['name' => 'Ranked Member', 'rank_id' => $rank->getKey(), 'unit_id' => $this->company->getKey()]);
        User::factory()->createQuietly(['name' => 'Someone Else', 'unit_id' => $this->company->getKey()]);

        Livewire::test(Roster::class)
            ->set('search', 'staff sergeant')
            ->assertSee('Ranked Member')
            ->assertDontSee('Someone Else');
    }

    public function test_rank_and_status_filters_narrow_members(): void
    {
        $member = User::factory()->createQuietly(['name' => 'Filtered Member', 'unit_id' => $this->company->getKey()]);
        User::factory()->createQuietly(['name' => 'Excluded Member', 'unit_id' => $this->company->getKey()]);

        Livewire::test(Roster::class)
            ->set('rankId', $member->rank_id)
            ->assertSee('Filtered Member')
            ->assertDontSee('Excluded Member')
            ->call('resetFilters')
            ->set('statusId', $member->status_id)
            ->assertSee('Filtered Member')
            ->assertDontSee('Excluded Member');
    }

    public function test_online_filter_only_shows_online_members(): void
    {
        $online = User::factory()->createQuietly(['name' => 'Online Member', 'unit_id' => $this->company->getKey()]);
        User::factory()->createQuietly(['name' => 'Offline Member', 'unit_id' => $this->company->getKey()]);

        Cache::tags('users_online')->put('user_online_'.$online->getKey(), true);

        Livewire::test(Roster::class)
            ->set('onlineOnly', true)
            ->assertSee('Online Member')
            ->assertDontSee('Offline Member');
    }

    public function test_unit_filter_scopes_the_roster_to_that_units_subtree(): void
    {
        User::factory()->createQuietly(['name' => 'Company Commander', 'unit_id' => $this->company->getKey()]);
        User::factory()->createQuietly(['name' => 'Platoon Leader', 'unit_id' => $this->platoon->getKey()]);

        Livewire::test(Roster::class)
            ->set('unitId', $this->platoon->getKey())
            ->assertSee('Platoon Leader')
            ->assertDontSee('Company Commander');
    }

    public function test_layouts_can_be_switched(): void
    {
        User::factory()->createQuietly(['name' => 'Card Member', 'unit_id' => $this->company->getKey()]);

        Livewire::test(Roster::class)
            ->call('setDisplay', 'cards')
            ->assertSet('display', 'cards')
            ->assertSee('Card Member')
            ->call('setDisplay', 'chart')
            ->assertSet('display', 'chart')
            ->assertSee('Print')
            ->assertSee('Card Member')
            ->call('setDisplay', 'invalid')
            ->assertSet('display', 'list');
    }

    public function test_it_renders_the_manual_roster_with_slots(): void
    {
        DashboardSettings::fake(['roster_mode' => RosterMode::MANUAL]);
        SettingsService::flush(DashboardSettings::class);

        $slot = Slot::factory()->createQuietly(['name' => 'Squad Leader Slot']);
        $this->company->slots()->attach($slot);
        $pivotId = DB::table('units_slots')->where('unit_id', $this->company->getKey())->value('id');

        User::factory()->createQuietly(['name' => 'Slotted Member', 'unit_id' => $this->company->getKey(), 'unit_slot_id' => $pivotId]);

        Livewire::test(Roster::class)
            ->assertOk()
            ->assertSeeInOrder(['Alpha Company', 'Squad Leader Slot', 'Slotted Member']);
    }

    public function test_members_show_readiness_and_promotion_badges(): void
    {
        $senior = Rank::factory()->createQuietly(['name' => 'Sergeant', 'order' => 1]);
        $junior = Rank::factory()->createQuietly(['name' => 'Corporal', 'order' => 2]);
        RankRequirement::factory()->createQuietly(['rank_id' => $senior->getKey(), 'min_days_in_service' => 0]);

        $member = User::factory()->createQuietly(['name' => 'Ready Member', 'rank_id' => $junior->getKey(), 'unit_id' => $this->company->getKey()]);

        QualificationRecord::factory()->createQuietly([
            'user_id' => $member->getKey(),
            'qualification_id' => Qualification::factory()->createQuietly()->getKey(),
            'expires_at' => now()->subDay(),
        ]);

        Livewire::test(Roster::class)
            ->assertSee('Ready Member')
            ->assertSee('Promotion Eligible')
            ->assertSee('Expired');
    }

    public function test_member_rows_list_formatted_time_in_grade(): void
    {
        User::factory()->createQuietly([
            'name' => 'Veteran Member',
            'unit_id' => $this->company->getKey(),
            'rank_id' => Rank::factory()->createQuietly()->getKey(),
            'created_at' => now()->subDays(1232),
        ]);

        Livewire::test(Roster::class)
            ->assertSee('Veteran Member')
            ->assertSee('1,232 days in grade');
    }

    public function test_query_count_does_not_grow_with_the_number_of_members(): void
    {
        User::factory()->count(3)->createQuietly(['unit_id' => $this->company->getKey()]);
        $baseline = $this->countRosterQueries();

        User::factory()->count(20)->createQuietly(['unit_id' => $this->company->getKey()]);
        $withMoreMembers = $this->countRosterQueries();

        $this->assertLessThanOrEqual($baseline + 5, $withMoreMembers, sprintf('Roster queries grew from %d to %d.', $baseline, $withMoreMembers));
    }

    protected function countRosterQueries(): int
    {
        DB::flushQueryLog();
        DB::connection('tenant')->enableQueryLog();

        Livewire::test(Roster::class)->assertOk();

        $count = count(DB::connection('tenant')->getQueryLog());

        DB::connection('tenant')->disableQueryLog();
        DB::connection('tenant')->flushQueryLog();

        return $count;
    }
}
