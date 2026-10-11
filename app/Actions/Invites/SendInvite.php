<?php

declare(strict_types=1);

namespace App\Actions\Invites;

use App\Models\Enums\InviteType;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SendInvite
{
    /**
     * Invite someone to either create a brand-new profile or take ownership of an existing one. Any
     * outstanding invites for the same recipient or profile are revoked so only one link is ever live.
     *
     * @throws ValidationException
     */
    public static function handle(InviteType $type, string $email, ?string $name = null, ?User $user = null, ?User $invitedBy = null): Invite
    {
        Validator::make([
            'email' => $email,
            'name' => $name,
            'user' => $user?->getKey(),
        ], [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user?->getKey())],
            'name' => ['nullable', 'string', 'max:255'],
            'user' => [Rule::requiredIf($type === InviteType::CLAIM)],
        ], [
            'email.unique' => 'A user with this email address already exists.',
            'user.required' => 'A profile must be selected to send a claim invitation.',
        ])->validate();

        $invite = DB::transaction(function () use ($type, $email, $name, $user, $invitedBy): Invite {
            Invite::query()
                ->pending()
                ->where(fn ($query) => $query->where('email', $email)->when($user, fn ($query) => $query->orWhere('user_id', $user->getKey())))
                ->update(['revoked_at' => now()]);

            return Invite::create([
                'type' => $type,
                'email' => $email,
                'name' => $type === InviteType::CLAIM ? $user?->name : $name,
                'user_id' => $type === InviteType::CLAIM ? $user?->getKey() : null,
                'invited_by_id' => $invitedBy?->getKey(),
                'token' => Invite::hashToken(Str::random(64)),
            ]);
        });

        return DeliverInvite::handle($invite);
    }
}
