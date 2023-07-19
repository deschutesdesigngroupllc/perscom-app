<?php

namespace Tests\Feature\Tenant\Notifications\Tenant;

use App\Models\ServiceRecord;
use App\Notifications\Tenant\NewServiceRecord;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class NewServiceRecordTest extends TenantTestCase
{
    public function test_notification_is_sent()
    {
        Notification::fake();

        ServiceRecord::factory()->for($this->user)->create();

        Notification::assertSentTo($this->user, NewServiceRecord::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->user);
            $mail->assertTo($this->user->email);

            $nova = $notification->toNova();
            $this->assertSame('A new service record has been added to your personnel file.', $nova->message);

            return true;
        });
    }
}
