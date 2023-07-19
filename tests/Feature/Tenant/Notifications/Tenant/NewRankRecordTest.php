<?php

namespace Tests\Feature\Tenant\Notifications\Tenant;

use App\Models\RankRecord;
use App\Notifications\Tenant\NewRankRecord;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class NewRankRecordTest extends TenantTestCase
{
    public function test_notification_is_sent()
    {
        Notification::fake();

        RankRecord::factory()->for($this->user)->create();

        Notification::assertSentTo($this->user, NewRankRecord::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->user);
            $mail->assertTo($this->user->email);

            $nova = $notification->toNova();
            $this->assertSame('A new rank record has been added to your personnel file.', $nova->message);

            return true;
        });
    }
}
