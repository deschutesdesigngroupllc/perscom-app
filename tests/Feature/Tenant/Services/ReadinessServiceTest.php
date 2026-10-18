<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Models\Credential;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\TrainingRecord;
use App\Models\Unit;
use App\Models\User;
use App\Services\ReadinessService;
use Tests\Feature\Tenant\TenantTestCase;

class ReadinessServiceTest extends TenantTestCase
{
    public function test_members_with_lapsed_items_are_not_ready_unless_renewed(): void
    {
        $qualification = Qualification::factory()->createQuietly();
        $credential = Credential::factory()->createQuietly();

        $lapsedQualification = User::factory()->createQuietly();
        $renewed = User::factory()->createQuietly();
        $lapsedCredential = User::factory()->createQuietly();

        $this->qualify($lapsedQualification, $qualification, now()->subDay());
        $this->qualify($renewed, $qualification, now()->subDay());
        $this->qualify($renewed, $qualification, now()->addYear());

        $training = TrainingRecord::factory()->createQuietly(['user_id' => $lapsedCredential->getKey()]);
        $training->credentials()->attach($credential->getKey(), ['expires_at' => now()->subDays(3)]);

        $this->assertEqualsCanonicalizing(
            [$lapsedQualification->getKey(), $lapsedCredential->getKey()],
            ReadinessService::notReadyUserIds()->all(),
        );
    }

    public function test_summaries_calculate_ready_percentage_attendance_rate_and_expiring_per_unit(): void
    {
        $unit = Unit::factory()->createQuietly(['name' => 'Alpha']);
        $qualification = Qualification::factory()->createQuietly();

        $ready = User::factory()->createQuietly(['unit_id' => $unit->getKey()]);
        $lapsed = User::factory()->createQuietly(['unit_id' => $unit->getKey()]);
        $expiringSoon = User::factory()->createQuietly(['unit_id' => $unit->getKey()]);
        $alsoReady = User::factory()->createQuietly(['unit_id' => $unit->getKey()]);

        $this->qualify($lapsed, $qualification, now()->subDay());
        $this->qualify($expiringSoon, $qualification, now()->addDays(10));

        $this->attend($ready, EventRegistrationStatus::ATTENDED);
        $this->attend($ready, EventRegistrationStatus::ATTENDED);
        $this->attend($ready, EventRegistrationStatus::ATTENDED);
        $this->attend($alsoReady, EventRegistrationStatus::NO_SHOW);

        $summary = ReadinessService::summaries()->firstWhere('label', 'Alpha');

        $this->assertSame(4, $summary->members);
        $this->assertSame(75.0, $summary->readyPercentage);
        $this->assertSame(75.0, $summary->attendanceRate);
        $this->assertSame(1, $summary->expiring);
        $this->assertSame('Organization', ReadinessService::summaries()->first()->label);
    }

    public function test_attendance_rate_is_null_without_recorded_attendance(): void
    {
        $unit = Unit::factory()->createQuietly(['name' => 'Bravo']);
        User::factory()->createQuietly(['unit_id' => $unit->getKey()]);

        $this->assertNull(ReadinessService::summaries()->firstWhere('label', 'Bravo')->attendanceRate);
    }

    public function test_expiring_items_include_qualifications_and_credentials_within_the_window(): void
    {
        $user = User::factory()->createQuietly(['name' => 'Jane Doe']);
        $qualification = Qualification::factory()->createQuietly(['name' => 'Airborne']);
        $credential = Credential::factory()->createQuietly(['name' => 'EMT']);

        $this->qualify($user, $qualification, now()->addDays(5));
        $this->qualify($user, $qualification, now()->addDays(60));

        $training = TrainingRecord::factory()->createQuietly(['user_id' => $user->getKey()]);
        $training->credentials()->attach($credential->getKey(), ['expires_at' => now()->addDays(2)]);

        $items = ReadinessService::expiringItems()->values();

        $this->assertCount(2, $items);
        $this->assertSame('EMT', $items[0]['item']);
        $this->assertSame('Credential', $items[0]['type']);
        $this->assertSame('Airborne', $items[1]['item']);
        $this->assertSame('Jane Doe', $items[1]['member']);
    }

    protected function qualify(User $user, Qualification $qualification, mixed $expiresAt): void
    {
        QualificationRecord::factory()->createQuietly([
            'user_id' => $user->getKey(),
            'qualification_id' => $qualification->getKey(),
            'expires_at' => $expiresAt,
        ]);
    }

    protected function attend(User $user, EventRegistrationStatus $status): void
    {
        $user->events()->attach(Event::factory()->createQuietly()->getKey(), ['status' => $status->value]);
    }
}
