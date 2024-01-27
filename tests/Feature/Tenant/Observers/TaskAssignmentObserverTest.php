<?php

namespace Tests\Feature\Tenant\Observers;

use App\Models\TaskAssignment;
use App\Models\User;
use App\Notifications\Tenant\NewTaskAssignment;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class TaskAssignmentObserverTest extends TenantTestCase
{
    public function test_create_task_assignment_notification_sent()
    {
        Notification::fake();

        $assignment = TaskAssignment::factory()->for($user = User::factory()->create())->create();

        Notification::assertSentTo($user, NewTaskAssignment::class, function ($notification, $channels) use ($assignment) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($assignment->user);
            $mail->assertTo($assignment->user->email);

            $nova = $notification->toNova();
            $this->assertSame('A new task has been assigned to you.', $nova->message);

            return true;
        });
    }
}
