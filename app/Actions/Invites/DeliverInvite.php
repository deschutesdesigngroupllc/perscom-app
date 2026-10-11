<?php

declare(strict_types=1);

namespace App\Actions\Invites;

use App\Models\Invite;
use App\Notifications\User\Invited;
use App\Services\SettingsService;
use App\Settings\OrganizationSettings;
use Illuminate\Support\Facades\Notification;

class DeliverInvite
{
    /**
     * Issue a fresh token for the invite and email the acceptance link to the recipient. Any previously
     * delivered link for the invite is invalidated because only the newest token hash is stored.
     */
    public static function handle(Invite $invite): Invite
    {
        $token = $invite->refreshToken();

        Notification::route('mail', $invite->email)->notify(new Invited(
            invite: $invite,
            url: route('app.invites.accept', ['token' => $token]),
            organization: SettingsService::get(OrganizationSettings::class, 'name', config('app.name')),
        ));

        return $invite;
    }
}
