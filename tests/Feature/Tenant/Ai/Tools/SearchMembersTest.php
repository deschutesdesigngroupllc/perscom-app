<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Ai\Tools;

use App\Ai\Tools\SearchMembers;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\Unit;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Laravel\Ai\Tools\Request;
use Tests\Feature\Tenant\TenantTestCase;

class SearchMembersTest extends TenantTestCase
{
    private ?User $admin = null;

    public function test_it_filters_by_unit_and_rank(): void
    {
        $unit = Unit::factory()->createQuietly();
        $rank = Rank::factory()->createQuietly();
        $match = User::factory()->createQuietly(['unit_id' => $unit->getKey(), 'rank_id' => $rank->getKey()]);
        User::factory()->createQuietly(['unit_id' => $unit->getKey()]);
        User::factory()->createQuietly(['rank_id' => $rank->getKey()]);

        $this->assertSame([$match->getKey()], $this->search(['unit_id' => $unit->getKey(), 'rank_id' => $rank->getKey()]));
    }

    public function test_it_filters_by_days_in_grade_using_the_latest_rank_record(): void
    {
        $veteran = User::factory()->createQuietly();
        $recent = User::factory()->createQuietly();
        RankRecord::factory()->for($veteran)->createQuietly(['created_at' => now()->subDays(45)]);
        RankRecord::factory()->for($recent)->createQuietly(['created_at' => now()->subDays(45)]);
        RankRecord::factory()->for($recent)->createQuietly(['created_at' => now()->subDays(5)]);

        $this->assertSame([$veteran->getKey()], $this->search(['min_days_in_grade' => 30]));
        $this->assertSame([$recent->getKey()], $this->search(['max_days_in_grade' => 10]));
    }

    public function test_it_filters_by_held_and_missing_qualifications(): void
    {
        $qualification = Qualification::factory()->createQuietly();
        $qualified = User::factory()->createQuietly();
        $unqualified = User::factory()->createQuietly();
        QualificationRecord::factory()->for($qualified)->createQuietly(['qualification_id' => $qualification->getKey()]);

        $this->assertSame([$qualified->getKey()], $this->search(['has_qualification_id' => $qualification->getKey()]));
        $this->assertContains($unqualified->getKey(), $this->search(['missing_qualification_id' => $qualification->getKey()]));
        $this->assertNotContains($qualified->getKey(), $this->search(['missing_qualification_id' => $qualification->getKey()]));
    }

    public function test_it_filters_by_last_seen(): void
    {
        $active = User::factory()->createQuietly(['last_seen_at' => now()->subDay()]);
        $inactive = User::factory()->createQuietly(['last_seen_at' => now()->subDays(60)]);

        $this->assertSame([$active->getKey()], $this->search(['last_seen_after' => now()->subDays(7)->toDateString()]));
        $this->assertContains($inactive->getKey(), $this->search(['last_seen_before' => now()->subDays(30)->toDateString()]));
        $this->assertNotContains($active->getKey(), $this->search(['last_seen_before' => now()->subDays(30)->toDateString()]));
    }

    public function test_it_filters_by_events_attended_within_a_window(): void
    {
        $attendee = User::factory()->createQuietly();
        $absentee = User::factory()->createQuietly();
        $recentEvent = Event::factory()->createQuietly(['starts' => now()->subDays(3), 'ends' => now()->subDays(3)->addHour()]);
        $oldEvent = Event::factory()->createQuietly(['starts' => now()->subDays(90), 'ends' => now()->subDays(90)->addHour()]);
        $attendee->events()->attach($recentEvent, ['status' => EventRegistrationStatus::ATTENDED->value]);
        $absentee->events()->attach($oldEvent, ['status' => EventRegistrationStatus::ATTENDED->value]);
        $absentee->events()->attach($recentEvent, ['status' => EventRegistrationStatus::NO_SHOW->value]);

        $this->assertSame([$attendee->getKey()], $this->search(['min_events_attended' => 1, 'attended_within_days' => 30]));
    }

    public function test_it_refuses_actors_who_cannot_view_members(): void
    {
        $actor = User::factory()->withoutRoles()->createQuietly();

        $result = (string) new SearchMembers($actor)->handle(new Request([]));

        $this->assertSame('You are not allowed to view members.', $result);
    }

    public function test_it_reports_days_in_grade(): void
    {
        $member = User::factory()->createQuietly(['name' => 'Zed Tester']);
        RankRecord::factory()->for($member)->createQuietly(['created_at' => now()->subDays(12)]);

        $result = json_decode((string) new SearchMembers($this->superAdmin())->handle(new Request(['name' => 'Zed Tester'])), true);

        $this->assertSame(12, $result['members'][0]['days_in_grade']);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, int>
     */
    private function search(array $filters): array
    {
        $result = json_decode((string) new SearchMembers($this->superAdmin())->handle(new Request($filters)), true);

        return collect($result['members'])->pluck('id')->reject(fn (int $id): bool => $id === $this->superAdmin()->getKey())->sort()->values()->all();
    }

    private function superAdmin(): User
    {
        if ($this->admin instanceof User) {
            return $this->admin;
        }

        $this->admin = User::factory()->unassigned()->createQuietly();
        $this->admin->assignRole(Utils::getSuperAdminName());

        return $this->admin;
    }
}
