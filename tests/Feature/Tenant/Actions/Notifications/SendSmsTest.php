<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Notifications;

use App\Actions\Notifications\SendSms;
use App\Models\User;
use App\Services\TwilioService;
use Exception;
use Mockery;
use Mockery\MockInterface;
use Tests\Feature\Tenant\TenantTestCase;
use Twilio\Rest\Api\V2010\Account\MessageInstance;

class SendSmsTest extends TenantTestCase
{
    public function test_it_returns_false_when_the_user_has_no_phone_number(): void
    {
        $user = User::factory()->createQuietly([
            'phone_number' => null,
        ]);

        $this->assertFalse(SendSms::handle($user, 'Hello there.'));
    }

    public function test_it_returns_the_message_instance_on_success(): void
    {
        $user = User::factory()->createQuietly([
            'phone_number' => '+15555555555',
        ]);

        $instance = Mockery::mock(MessageInstance::class);

        $this->mock(TwilioService::class, function (MockInterface $mock) use ($instance): void {
            $mock->shouldReceive('sendSms')
                ->once()
                ->with('+15555555555', 'Hello there.')
                ->andReturn($instance);
        });

        $this->assertSame($instance, SendSms::handle($user, 'Hello there.'));
    }

    public function test_it_reports_and_returns_false_when_the_service_throws(): void
    {
        $user = User::factory()->createQuietly([
            'phone_number' => '+15555555555',
        ]);

        $this->mock(TwilioService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendSms')
                ->once()
                ->andThrow(new Exception('Twilio failure'));
        });

        $this->assertFalse(SendSms::handle($user, 'Hello there.'));
    }
}
