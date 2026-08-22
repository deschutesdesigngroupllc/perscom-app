<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Tenant;

use App\Jobs\Tenant\CalculateSchedules;
use App\Models\Enums\ScheduleEndType;
use App\Models\Event;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class CalculateSchedulesTest extends TenantTestCase
{
    public function test_work_recalculates_the_next_and_last_occurrence_for_each_schedule(): void
    {
        // Schedules are polymorphic pivots that must be created through their
        // owning model so the repeatable morph keys are populated.
        Event::factory()->withSchedule()->createQuietly();

        // The model populates the occurrences on save, so clear them via the
        // query builder (avoiding the pivot instance) to give the job real work.
        Schedule::query()->update([
            'next_occurrence' => null,
            'last_occurrence' => null,
        ]);

        (new CalculateSchedules($this->tenant->getKey()))->work();

        $schedule = Schedule::query()->firstOrFail();

        $this->assertNotNull($schedule->next_occurrence);
        $this->assertEquals(
            ScheduleService::nextOccurrence($schedule)?->toDateTimeString(),
            $schedule->next_occurrence?->toDateTimeString()
        );
        // A daily schedule that never ends has no last occurrence.
        $this->assertSame(ScheduleEndType::NEVER, $schedule->end_type);
        $this->assertNull($schedule->last_occurrence);
    }

    public function test_it_is_routed_to_the_central_connection_when_tenancy_is_enabled(): void
    {
        Queue::fake();
        config(['tenancy.enabled' => true]);

        CalculateSchedules::dispatch($this->tenant->getKey());

        Queue::assertPushed(
            CalculateSchedules::class,
            fn (CalculateSchedules $job): bool => $job->connection === 'central'
                && $job->tenantKey === $this->tenant->getKey()
        );
    }
}
