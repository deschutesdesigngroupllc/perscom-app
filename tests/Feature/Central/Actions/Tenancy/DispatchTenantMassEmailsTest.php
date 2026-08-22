<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Actions\Tenancy;

use App\Actions\Tenancy\DispatchTenantMassEmails;
use App\Jobs\Central\SendMassEmail;
use App\Models\Mail;
use App\Models\Tenant;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Tests\Feature\Central\CentralTestCase;

class DispatchTenantMassEmailsTest extends CentralTestCase
{
    public function test_returns_null_when_tenancy_is_disabled(): void
    {
        Bus::fake();
        config(['tenancy.enabled' => false]);

        $mail = new Mail(['subject' => 'Hello', 'content' => 'World', 'send_now' => true]);

        $this->assertNull(DispatchTenantMassEmails::handle($mail));
        Bus::assertNothingBatched();
    }

    public function test_returns_null_when_mail_has_already_been_sent(): void
    {
        Bus::fake();
        config(['tenancy.enabled' => true]);

        $mail = new Mail(['subject' => 'Hello', 'content' => 'World', 'send_now' => true]);
        $mail->sent_at = now();

        $this->assertNull(DispatchTenantMassEmails::handle($mail));
        Bus::assertNothingBatched();
    }

    public function test_returns_null_when_not_scheduled_and_not_immediate(): void
    {
        Bus::fake();
        config(['tenancy.enabled' => true]);

        $mail = new Mail(['subject' => 'Hello', 'content' => 'World', 'send_now' => false]);

        $this->assertNull(DispatchTenantMassEmails::handle($mail));
        Bus::assertNothingBatched();
    }

    public function test_batches_a_job_per_recipient_tenant(): void
    {
        Bus::fake();
        config(['tenancy.enabled' => true]);

        $tenant = Tenant::factory()->createQuietly();

        $mail = new Mail([
            'subject' => 'Hello',
            'content' => 'World',
            'send_now' => true,
            'recipients' => [$tenant->getKey()],
        ]);

        DispatchTenantMassEmails::handle($mail);

        Bus::assertBatched(fn (PendingBatch $batch): bool => $batch->name === 'Send Tenant Mass Emails'
            && $batch->jobs->isNotEmpty()
            && $batch->jobs->first() instanceof SendMassEmail);
    }
}
