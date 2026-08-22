<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Central;

use App\Jobs\Central\SendMassEmail;
use App\Mail\System\MassEmail;
use App\Models\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Mail as MailFacade;
use Tests\Feature\Tenant\TenantTestCase;

class SendMassEmailTest extends TenantTestCase
{
    public function test_it_sends_the_mass_email_and_marks_the_record_as_sent(): void
    {
        MailFacade::fake();

        $user = User::factory()->create();

        $mail = Mail::withoutEvents(fn (): Mail => Mail::create([
            'subject' => 'Operator Update',
            'content' => 'Hello tenants.',
            'recipients' => [],
            'send_now' => true,
        ]));

        new SendMassEmail($user, $mail)->handle();

        MailFacade::assertSent(MassEmail::class);
        $this->assertNotNull($mail->fresh()->sent_at);
    }

    public function test_it_delays_scheduled_mail_on_the_central_connection(): void
    {
        $user = User::factory()->create();

        $mail = Mail::withoutEvents(fn (): Mail => Mail::create([
            'subject' => 'Scheduled Update',
            'content' => 'Later.',
            'recipients' => [],
            'send_now' => false,
            'send_at' => now()->addDay(),
        ]));

        $job = new SendMassEmail($user, $mail);

        $this->assertNotNull($job->delay);
        $this->assertSame('central', $job->connection);
    }
}
