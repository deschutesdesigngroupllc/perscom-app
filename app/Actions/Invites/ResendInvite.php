<?php

declare(strict_types=1);

namespace App\Actions\Invites;

use App\Models\Enums\InviteType;
use App\Models\Invite;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResendInvite
{
    /**
     * Reactivate an unaccepted invite and deliver a fresh link. Any other outstanding invites for the
     * same recipient or profile are revoked so only one link is ever live. A claim invite cannot be
     * resent once its profile has been claimed.
     *
     * @throws ValidationException
     */
    public static function handle(Invite $invite): Invite
    {
        $invite = DB::transaction(function () use ($invite): Invite {
            /** @var Invite $invite */
            $invite = Invite::query()->with('user')->lockForUpdate()->findOrFail($invite->getKey());

            throw_if(
                filled($invite->accepted_at),
                ValidationException::withMessages(['invite' => 'This invitation has already been accepted.'])
            );

            throw_if(
                $invite->type === InviteType::CLAIM && (blank($invite->user) || filled($invite->user->password)),
                ValidationException::withMessages(['invite' => 'This profile has already been claimed.'])
            );

            Invite::query()
                ->pending()
                ->whereKeyNot($invite->getKey())
                ->where(fn ($query) => $query->where('email', $invite->email)->when(filled($invite->user_id), fn ($query) => $query->orWhere('user_id', $invite->user_id)))
                ->update(['revoked_at' => now()]);

            $invite->update(['revoked_at' => null]);

            return $invite;
        });

        return DeliverInvite::handle($invite);
    }
}
