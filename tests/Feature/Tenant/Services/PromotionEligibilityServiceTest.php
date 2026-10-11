<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Models\Credential;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\RankRequirement;
use App\Models\TrainingRecord;
use App\Models\Unit;
use App\Models\User;
use App\Services\PromotionEligibilityService;
use Tests\Feature\Tenant\TenantTestCase;

class PromotionEligibilityServiceTest extends TenantTestCase
{
    protected Rank $senior;

    protected Rank $junior;

    protected function setUp(): void
    {
        parent::setUp();

        $this->senior = Rank::factory()->createQuietly(['name' => 'Sergeant', 'order' => 1]);
        $this->junior = Rank::factory()->createQuietly(['name' => 'Corporal', 'order' => 2]);
    }

    public function test_next_rank_is_the_more_senior_rank_by_order(): void
    {
        $this->assertTrue(PromotionEligibilityService::nextRankFor($this->junior)->is($this->senior));
        $this->assertNull(PromotionEligibilityService::nextRankFor($this->senior));
    }

    public function test_member_without_configured_requirements_is_not_eligible(): void
    {
        $user = $this->juniorMember();

        $evaluation = PromotionEligibilityService::evaluate($user);

        $this->assertFalse($evaluation->eligible);
        $this->assertTrue($evaluation->nextRank->is($this->senior));
        $this->assertSame('No promotion requirements are configured for the next rank.', $evaluation->reason);
    }

    public function test_member_at_the_most_senior_rank_is_not_eligible(): void
    {
        $user = User::factory()->createQuietly(['rank_id' => $this->senior->getKey()]);

        $evaluation = PromotionEligibilityService::evaluate($user);

        $this->assertFalse($evaluation->eligible);
        $this->assertNull($evaluation->nextRank);
    }

    public function test_time_in_grade_uses_the_latest_rank_record(): void
    {
        $this->requirement(['min_days_in_grade' => 30]);
        $user = $this->juniorMember();

        RankRecord::factory()->createQuietly(['user_id' => $user->getKey(), 'rank_id' => $this->junior->getKey(), 'created_at' => now()->subDays(60)]);
        $this->assertTrue(PromotionEligibilityService::evaluate($user)->eligible);

        RankRecord::factory()->createQuietly(['user_id' => $user->getKey(), 'rank_id' => $this->junior->getKey(), 'created_at' => now()->subDays(10)]);
        $evaluation = PromotionEligibilityService::evaluate($user->fresh());

        $this->assertFalse($evaluation->eligible);
        $this->assertSame('10 of 30 days', $evaluation->unmetRequirements()[0]->detail);
    }

    public function test_time_in_service_requirement(): void
    {
        $this->requirement(['min_days_in_service' => 100]);

        $this->assertTrue(PromotionEligibilityService::evaluate($this->juniorMember(['created_at' => now()->subDays(150)]))->eligible);
        $this->assertFalse(PromotionEligibilityService::evaluate($this->juniorMember(['created_at' => now()->subDays(50)]))->eligible);
    }

    public function test_events_attended_requirement_counts_only_recent_attendance(): void
    {
        $this->requirement(['min_events_attended_90d' => 2]);
        $user = $this->juniorMember();

        $this->attend($user, EventRegistrationStatus::ATTENDED, now()->subDays(5));
        $this->attend($user, EventRegistrationStatus::NO_SHOW, now()->subDays(5));
        $this->attend($user, EventRegistrationStatus::ATTENDED, now()->subDays(120));

        $this->assertFalse(PromotionEligibilityService::evaluate($user)->eligible);

        $this->attend($user, EventRegistrationStatus::ATTENDED, now()->subDays(20));

        $this->assertTrue(PromotionEligibilityService::evaluate($user)->eligible);
    }

    public function test_qualification_requirement_ignores_expired_qualifications(): void
    {
        $qualification = Qualification::factory()->createQuietly(['name' => 'Airborne']);
        $this->requirement(['required_qualification_ids' => [$qualification->getKey()]]);
        $user = $this->juniorMember();

        QualificationRecord::factory()->createQuietly([
            'user_id' => $user->getKey(),
            'qualification_id' => $qualification->getKey(),
            'expires_at' => now()->subDay(),
        ]);

        $evaluation = PromotionEligibilityService::evaluate($user);
        $this->assertFalse($evaluation->eligible);
        $this->assertSame('Airborne', $evaluation->unmetRequirements()[0]->detail);

        QualificationRecord::factory()->createQuietly([
            'user_id' => $user->getKey(),
            'qualification_id' => $qualification->getKey(),
            'expires_at' => null,
        ]);

        $this->assertTrue(PromotionEligibilityService::evaluate($user)->eligible);
    }

    public function test_credential_requirement_ignores_expired_credentials(): void
    {
        $credential = Credential::factory()->createQuietly();
        $this->requirement(['required_credential_ids' => [$credential->getKey()]]);
        $user = $this->juniorMember();

        $expired = TrainingRecord::factory()->createQuietly(['user_id' => $user->getKey()]);
        $expired->credentials()->attach($credential->getKey(), ['issued_at' => now()->subYear(), 'expires_at' => now()->subDay()]);

        $this->assertFalse(PromotionEligibilityService::evaluate($user)->eligible);

        $current = TrainingRecord::factory()->createQuietly(['user_id' => $user->getKey()]);
        $current->credentials()->attach($credential->getKey(), ['issued_at' => now(), 'expires_at' => now()->addYear()]);

        $this->assertTrue(PromotionEligibilityService::evaluate($user)->eligible);
    }

    public function test_eligible_returns_only_eligible_members_and_filters_by_unit(): void
    {
        $this->requirement(['min_days_in_service' => 30]);
        $unit = Unit::factory()->createQuietly();

        $eligibleInUnit = $this->juniorMember(['created_at' => now()->subDays(60), 'unit_id' => $unit->getKey()]);
        $eligibleElsewhere = $this->juniorMember(['created_at' => now()->subDays(60)]);
        $notEligible = $this->juniorMember(['created_at' => now()->subDays(5), 'unit_id' => $unit->getKey()]);

        $this->assertEqualsCanonicalizing(
            [$eligibleInUnit->getKey(), $eligibleElsewhere->getKey()],
            PromotionEligibilityService::eligible()->keys()->all(),
        );
        $this->assertSame([$eligibleInUnit->getKey()], PromotionEligibilityService::eligible($unit->getKey())->keys()->all());
        $this->assertSame([$notEligible->getKey()], PromotionEligibilityService::notYetEligible($unit->getKey())->keys()->all());
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function requirement(array $attributes): RankRequirement
    {
        return RankRequirement::factory()->createQuietly(array_merge(['rank_id' => $this->senior->getKey()], $attributes));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function juniorMember(array $attributes = []): User
    {
        return User::factory()->createQuietly(array_merge(['rank_id' => $this->junior->getKey()], $attributes));
    }

    protected function attend(User $user, EventRegistrationStatus $status, mixed $when): void
    {
        $event = Event::factory()->createQuietly();

        $user->events()->attach($event->getKey(), [
            'status' => $status->value,
            'created_at' => $when,
            'updated_at' => $when,
        ]);
    }
}
