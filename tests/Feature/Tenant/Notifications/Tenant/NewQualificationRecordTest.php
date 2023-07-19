<?php

namespace Tests\Feature\Tenant\Notifications\Tenant;

use App\Models\QualificationRecord;
use App\Notifications\Tenant\NewQualificationRecord;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class NewQualificationRecordTest extends TenantTestCase
{
    public function test_notification_is_sent()
    {
        Notification::fake();

        QualificationRecord::factory()->for($this->user)->create();

        Notification::assertSentTo($this->user, NewQualificationRecord::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->user);
            $mail->assertTo($this->user->email);

            $nova = $notification->toNova();
            $this->assertSame('A new qualification record has been added to your personnel file.', $nova->message);

            return true;
        });
    }
}
