<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Observers;

use App\Models\Mail;
use Illuminate\Support\Facades\Bus;
use Tests\Feature\Central\CentralTestCase;

class MailObserverTest extends CentralTestCase
{
    public function test_creating_a_scheduled_mail_queues_one_batch(): void
    {
        config(['tenancy.enabled' => true]);
        Bus::fake();

        $this->scheduledMail();

        $this->assertCount(1, Bus::dispatchedBatches());
    }

    public function test_editing_a_scheduled_mail_does_not_queue_it_again(): void
    {
        config(['tenancy.enabled' => true]);
        Bus::fake();

        $mail = $this->scheduledMail();
        $mail->update(['subject' => 'Range Day Moved (corrected)', 'content' => 'Saturday, not Sunday.']);

        $this->assertCount(1, Bus::dispatchedBatches());
    }

    public function test_rescheduling_a_mail_queues_it_for_the_new_time(): void
    {
        config(['tenancy.enabled' => true]);
        Bus::fake();

        $mail = $this->scheduledMail();
        $mail->update(['send_at' => now()->addDays(2)]);

        $this->assertCount(2, Bus::dispatchedBatches());
    }

    private function scheduledMail(): Mail
    {
        return Mail::create([
            'subject' => 'Range Day Moved',
            'content' => 'Saturday.',
            'recipients' => [],
            'send_now' => false,
            'send_at' => now()->addDay(),
        ]);
    }
}
