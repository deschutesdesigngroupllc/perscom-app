<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Mail\Tenant;

use App\Mail\Tenant\ApiExpirationReminder;
use Exception;
use Illuminate\Support\Carbon;
use ReflectionException;
use Tests\Feature\Tenant\TenantTestCase;

class ApiExpirationReminderTest extends TenantTestCase
{
    /**
     * @throws Exception
     */
    public function test_email_subject_contains_expiration_time(): void
    {
        $expiresAt = Carbon::now()->addDays(7);
        $mail = new ApiExpirationReminder('Test API Key', $expiresAt);

        $envelope = $mail->envelope();

        $this->assertStringContainsString('You have an API key expiring', $envelope->subject);
        $this->assertStringContainsString('6 days', $envelope->subject);
    }

    /**
     * @throws Exception
     */
    public function test_email_subject_with_one_day_expiration(): void
    {
        $expiresAt = Carbon::now()->addDay();
        $mail = new ApiExpirationReminder('Test API Key', $expiresAt);

        $envelope = $mail->envelope();

        $this->assertStringContainsString('You have an API key expiring', $envelope->subject);
        $this->assertStringContainsString('23 hours', $envelope->subject);
    }

    /**
     * @throws Exception
     */
    public function test_email_subject_with_thirty_days_expiration(): void
    {
        $expiresAt = Carbon::now()->addDays(30);
        $mail = new ApiExpirationReminder('Test API Key', $expiresAt);

        $envelope = $mail->envelope();

        $this->assertStringContainsString('You have an API key expiring', $envelope->subject);
        $this->assertStringContainsString('4 weeks', $envelope->subject);
    }

    public function test_email_content_receives_correct_data(): void
    {
        $name = 'Production API Key';
        $expiresAt = Carbon::now()->addDays(7);
        $mail = new ApiExpirationReminder($name, $expiresAt);

        $content = $mail->content();

        $this->assertEquals('emails.tenant.api-expiration-reminder', $content->markdown);
        $this->assertEquals($name, $content->with['name']);
        $this->assertEquals($expiresAt, $content->with['expires_at']);
    }

    /**
     * @throws ReflectionException
     */
    public function test_email_can_be_rendered(): void
    {
        $name = 'Production API Key';
        $expiresAt = Carbon::now()->addDays(7);
        $mail = new ApiExpirationReminder($name, $expiresAt);

        $rendered = $mail->render();

        $this->assertIsString($rendered);
        $this->assertStringContainsString('API Key Expiration Reminder', $rendered);
        $this->assertStringContainsString($name, $rendered);
    }

    /**
     * @throws ReflectionException
     */
    public function test_email_contains_api_key_name(): void
    {
        $name = 'My Custom API Key';
        $expiresAt = Carbon::now()->addDays(14);
        $mail = new ApiExpirationReminder($name, $expiresAt);

        $rendered = $mail->render();

        $this->assertStringContainsString($name, $rendered);
    }

    /**
     * @throws ReflectionException
     */
    public function test_email_contains_formatted_expiration_date(): void
    {
        $name = 'Test API Key';
        $expiresAt = Carbon::parse('2025-12-25 10:00:00');
        $mail = new ApiExpirationReminder($name, $expiresAt);

        $rendered = $mail->render();

        $this->assertStringContainsString('December 25, 2025', $rendered);
    }

    public function test_email_is_queued(): void
    {
        $name = 'Test API Key';
        $expiresAt = Carbon::now()->addDays(7);
        $mail = new ApiExpirationReminder($name, $expiresAt);

        // ApiExpirationReminder should use the Queueable trait
        $this->assertObjectHasProperty('connection', $mail);
        $this->assertObjectHasProperty('queue', $mail);
    }
}
