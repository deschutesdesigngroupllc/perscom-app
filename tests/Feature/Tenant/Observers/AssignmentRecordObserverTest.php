<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Enums\WebhookEvent;
use App\Models\Position;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\Tenant\NewAssignmentRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class AssignmentRecordObserverTest extends TenantTestCase
{
    public function test_create_primary_assignment_record_assigns_user_properties(): void
    {
        $assignment = AssignmentRecord::factory()
            ->state([
                'status_id' => Status::factory(),
                'unit_id' => Unit::factory(),
                'position_id' => Position::factory(),
                'specialty_id' => Specialty::factory(),
                'type' => AssignmentRecordType::PRIMARY,
            ])
            ->for($user = User::factory()->create())
            ->create();

        $user = $user->fresh();

        $this->assertSame($assignment->unit->getKey(), $user->unit->getKey());
        $this->assertSame($assignment->position->getKey(), $user->position->getKey());
        $this->assertSame($assignment->specialty->getKey(), $user->specialty->getKey());
        $this->assertSame($assignment->status->getKey(), $user->status->getKey());
    }

    public function test_create_primary_assignment_record_removes_user_properties(): void
    {
        AssignmentRecord::factory()
            ->state([
                'status_id' => null,
                'unit_id' => null,
                'position_id' => null,
                'specialty_id' => null,
                'type' => AssignmentRecordType::PRIMARY,
            ])
            ->for($user = User::factory()->state([
                'status_id' => Status::factory(),
                'unit_id' => Unit::factory(),
                'position_id' => Position::factory(),
                'specialty_id' => Specialty::factory(),
            ])->create())
            ->create();

        $user = $user->fresh();

        $this->assertNull($user->unit);
        $this->assertNull($user->position);
        $this->assertNull($user->specialty);
        $this->assertNull($user->status);
    }

    public function test_create_secondary_assignment_record_does_not_assign_user_properties(): void
    {
        $assignment = AssignmentRecord::factory()
            ->state([
                'status_id' => Status::factory(),
                'unit_id' => Unit::factory(),
                'position_id' => Position::factory(),
                'specialty_id' => Specialty::factory(),
                'type' => AssignmentRecordType::SECONDARY,
            ])
            ->for($user = User::factory()->state([
                'status_id' => Status::factory(),
                'unit_id' => Unit::factory(),
                'position_id' => Position::factory(),
                'specialty_id' => Specialty::factory(),
            ])->create())
            ->create();

        $user = $user->fresh();

        $this->assertNotSame($assignment->unit->getKey(), $user->unit->getKey());
        $this->assertNotSame($assignment->position->getKey(), $user->position->getKey());
        $this->assertNotSame($assignment->specialty->getKey(), $user->specialty->getKey());
        $this->assertNotSame($assignment->status->getKey(), $user->status->getKey());
    }

    public function test_create_assignment_record_notification_sent()
    {
        Notification::fake();

        $assignment = AssignmentRecord::factory()->for($user = User::factory()->create())->create();

        Notification::assertSentTo($user, NewAssignmentRecord::class, function (NewAssignmentRecord $notification, $channels) use ($assignment) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($assignment->user);
            $mail->assertTo($assignment->user->email);

            return true;
        });
    }

    public function test_create_assignment_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::ASSIGNMENT_RECORD_CREATED],
        ])->create();

        AssignmentRecord::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_assignment_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::ASSIGNMENT_RECORD_UPDATED],
        ])->create();

        $assignment = AssignmentRecord::factory()->create();
        $assignment->update([
            'text' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_delete_assignment_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::ASSIGNMENT_RECORD_DELETED],
        ])->create();

        $assignment = AssignmentRecord::factory()->create();
        $assignment->delete();

        Queue::assertPushed(CallWebhookJob::class);
    }
}
