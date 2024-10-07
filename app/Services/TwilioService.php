<?php

declare(strict_types=1);

namespace App\Services;

use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;

class TwilioService
{
    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function sendSms(string $phoneNumber, string $message): MessageInstance
    {
        return $this->client()->messages->create($phoneNumber, [
            'from' => config('services.twilio.from'),
            'body' => $message,
        ]);
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
}
