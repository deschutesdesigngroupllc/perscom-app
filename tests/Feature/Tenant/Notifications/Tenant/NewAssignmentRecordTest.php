<?php

namespace Tests\Feature\Tenant\Notifications\Tenant;

use App\Models\AssignmentRecord;
use App\Notifications\Tenant\NewAssignmentRecord;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class NewAssignmentRecordTest extends TenantTestCase
{
    public function test_notification_is_sent()
    {
        Notification::fake();

        AssignmentRecord::factory()->for($this->user)->create();

        Notification::assertSentTo($this->user, NewAssignmentRecord::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->user);
            $mail->assertTo($this->user->email);

            $nova = $notification->toNova();
            $this->assertSame('A new assignment record has been added to your personnel file.', $nova->message);

            return true;
        });
    }
}
