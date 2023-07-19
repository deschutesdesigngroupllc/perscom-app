<?php

namespace Tests\Feature\Tenant\Notifications\Tenant;

use App\Models\CombatRecord;
use App\Notifications\Tenant\NewCombatRecord;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class NewCombatRecordTest extends TenantTestCase
{
    public function test_notification_is_sent()
    {
        Notification::fake();

        CombatRecord::factory()->for($this->user)->create();

        Notification::assertSentTo($this->user, NewCombatRecord::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->user);
            $mail->assertTo($this->user->email);

            $nova = $notification->toNova();
            $this->assertSame('A new combat record has been added to your personnel file.', $nova->message);

            return true;
        });
    }
}
