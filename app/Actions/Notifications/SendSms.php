<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\User;
use App\Services\TwilioService;
use Exception;
use Twilio\Rest\Api\V2010\Account\MessageInstance;

class SendSms
{
    public static function handle(User $user, string $message): false|MessageInstance
    {
        if (blank($user->phone_number)) {
            return false;
        }

        /** @var TwilioService $service */
        $service = app(TwilioService::class);

        try {
            return $service->sendSms(
                phoneNumber: $user->phone_number,
                message: $message
            );
        } catch (Exception $exception) {
            report($exception);

            return false;
        }
    }
}
