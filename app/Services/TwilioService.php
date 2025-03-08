<?php

declare(strict_types=1);

namespace App\Services;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Str;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;

class TwilioService
{
    public function __construct(public RateLimiter $limiter) {}

    public static function formatText(string $text): string
    {
        return Str::of($text)
            ->markdown()
            ->replace('<br>', ' ')
            ->deduplicate()
            ->stripTags()
            ->limit(255)
            ->toString();
    }

    public function toNotificationChannel(string $message): TwilioSmsMessage|TwilioMessage|false
    {
        return $this->withRateLimiter(fn (): TwilioMessage => (new TwilioSmsMessage)
            ->from(config('services.twilio.from'))
            ->content(TwilioService::formatText($message)));
    }

    public function sendSms(string $phoneNumber, string $message): MessageInstance|false
    {
        return $this->withRateLimiter(fn () => $this->client()->messages->create($phoneNumber, [
            'from' => config('services.twilio.from'),
            'body' => $message,
        ]));
    }

    /**
     * @throws ConfigurationException
     */
    protected function client(): Client
    {
        return new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
    }

    protected function withRateLimiter(Closure $callback): mixed
    {
        if (! tenancy()->initialized) {
            return false;
        }

        /** @var Closure $limiter */
        $limiter = $this->limiter->limiter('sms');

        /** @var Limit|bool $limit */
        $limit = $limiter(tenant());

        if (blank($limit) || ! $limit instanceof Limit) {
            return false;
        }

        if ($this->limiter->tooManyAttempts($limit->key, $limit->maxAttempts)) {
            return false;
        }

        $this->limiter->hit($limit->key, $limit->decaySeconds);

        return value($callback);
    }
}
