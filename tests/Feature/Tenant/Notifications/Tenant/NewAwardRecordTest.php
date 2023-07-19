<?php

namespace Tests\Feature\Tenant\Notifications\Tenant;

use App\Models\AwardRecord;
use App\Notifications\Tenant\NewAwardRecord;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class NewAwardRecordTest extends TenantTestCase
{
    public function test_notification_is_sent()
    {
        Notification::fake();

        AwardRecord::factory()->for($this->user)->create();

        Notification::assertSentTo($this->user, NewAwardRecord::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->user);
            $mail->assertTo($this->user->email);

            $nova = $notification->toNova();
            $this->assertSame('A new award record has been added to your personnel file.', $nova->message);

            return true;
        });
    }
}
