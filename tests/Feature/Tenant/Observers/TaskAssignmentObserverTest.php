<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Models\TaskAssignment;
use App\Models\User;
use App\Notifications\Tenant\NewTaskAssignment;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class TaskAssignmentObserverTest extends TenantTestCase
{
    public function test_create_task_assignment_notification_sent(): void
    {
        Notification::fake();

        $assignment = TaskAssignment::factory()->for($user = User::factory()->create())->create();

        Notification::assertSentTo($user, NewTaskAssignment::class, function (NewTaskAssignment $notification, iterable $channels) use ($assignment): true {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($assignment->user);
            $mail->assertTo($assignment->user->email);

            return true;
        });
    }
}
