<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Tenant;

use App\Jobs\Tenant\SendApiExpirationReminders;
use App\Mail\Tenant\ApiExpirationReminder;
use App\Models\PassportToken;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Tenant\TenantTestCase;

class SendApiExpirationRemindersTest extends TenantTestCase
{
    public function test_sends_email_for_tokens_expiring_in_one_month(): void
    {
        Mail::fake();

        PassportToken::factory()->createQuietly([
            'name' => 'Test Token - 1 Month',
            'expires_at' => now()->addMonth(),
        ]);

        $job = new SendApiExpirationReminders($this->tenant->getKey());
        $job->handle();

        Mail::assertSent(ApiExpirationReminder::class, function (ApiExpirationReminder $mail): bool {
            return $mail->expiresAt->isSameDay(now()->addMonth());
        });
    }

    public function test_sends_email_for_tokens_expiring_in_one_day(): void
    {
        Mail::fake();

        PassportToken::factory()->createQuietly([
            'name' => 'Test Token - 1 Day',
            'expires_at' => now()->addDay(),
        ]);

        $job = new SendApiExpirationReminders($this->tenant->getKey());
        $job->handle();

        Mail::assertSent(ApiExpirationReminder::class, function (ApiExpirationReminder $mail): bool {
            return $mail->expiresAt->isSameDay(now()->addDay());
        });
    }

    public function test_sends_emails_with_correct_token_names(): void
    {
        Mail::fake();

        $tokenName = 'Production API Key';
        PassportToken::factory()->createQuietly([
            'name' => $tokenName,
            'expires_at' => now()->addMonth(),
        ]);

        $job = new SendApiExpirationReminders($this->tenant->getKey());
        $job->handle();

        Mail::assertSent(ApiExpirationReminder::class, function (ApiExpirationReminder $mail) use ($tokenName): bool {
            return $mail->name === $tokenName;
        });
    }

    public function test_does_not_send_email_for_tokens_expiring_later(): void
    {
        Mail::fake();

        PassportToken::factory()->createQuietly([
            'name' => 'Token - 2 Days',
            'expires_at' => now()->addDays(2),
        ]);

        PassportToken::factory()->createQuietly([
            'name' => 'Token - 2 Months',
            'expires_at' => now()->addMonths(2),
        ]);

        $job = new SendApiExpirationReminders($this->tenant->getKey());
        $job->handle();

        Mail::assertNothingSent();
    }

    public function test_does_not_send_email_for_tokens_with_null_expiration(): void
    {
        Mail::fake();

        PassportToken::factory()->createQuietly([
            'name' => 'Never Expiring Token',
            'expires_at' => null,
        ]);

        $job = new SendApiExpirationReminders($this->tenant->getKey());
        $job->handle();

        Mail::assertNothingSent();
    }

    public function test_sends_multiple_emails_for_multiple_expiring_tokens(): void
    {
        Mail::fake();

        PassportToken::factory()->createQuietly([
            'name' => 'Token 1',
            'expires_at' => now()->addMonth(),
        ]);

        PassportToken::factory()->createQuietly([
            'name' => 'Token 2',
            'expires_at' => now()->addMonth(),
        ]);

        PassportToken::factory()->createQuietly([
            'name' => 'Token 3',
            'expires_at' => now()->addDay(),
        ]);

        $job = new SendApiExpirationReminders($this->tenant->getKey());
        $job->handle();

        Mail::assertSent(ApiExpirationReminder::class, 3);
    }

    public function test_emails_are_sent_to_tenant(): void
    {
        Mail::fake();

        PassportToken::factory()->createQuietly([
            'name' => 'Test Token',
            'expires_at' => now()->addMonth(),
        ]);

        $job = new SendApiExpirationReminders($this->tenant->getKey());
        $job->handle();

        Mail::assertSent(ApiExpirationReminder::class, function (ApiExpirationReminder $mail): bool {
            return $mail->hasTo($this->tenant->email);
        });
    }

    public function test_job_uses_central_connection(): void
    {
        $job = new SendApiExpirationReminders($this->tenant->getKey());

        $this->assertEquals('central', $job->connection);
    }

    public function test_job_handles_nonexistent_tenant_gracefully(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $job = new SendApiExpirationReminders(99999);
        $job->handle();
    }
}
