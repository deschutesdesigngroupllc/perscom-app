<?php

declare(strict_types=1);

namespace App\Actions\Invites;

use App\Models\Enums\InviteType;
use App\Models\Invite;
use App\Models\User;
use App\Traits\PasswordValidationRules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class AcceptInvite
{
    use PasswordValidationRules;

    /**
     * Redeem an invite. A create invite registers a new, pre-approved and verified user. A claim invite
     * hands the existing profile to the recipient by setting its email and password.
     *
     * @param  array<string, mixed>  $input
     *
     * @throws ValidationException
     */
    public static function handle(Invite $invite, #[SensitiveParameter] array $input): User
    {
        return DB::transaction(function () use ($invite, $input): User {
            /** @var Invite $invite */
            $invite = Invite::query()->lockForUpdate()->findOrFail($invite->getKey());

            $profile = $invite->type === InviteType::CLAIM ? $invite->user : null;

            throw_if(
                ! $invite->isPending() || ($invite->type === InviteType::CLAIM && blank($profile)),
                ValidationException::withMessages(['token' => 'This invitation is no longer valid.'])
            );

            Validator::make([...$input, 'email' => $invite->email], [
                'name' => [Rule::requiredIf($invite->type === InviteType::CREATE), 'nullable', 'string', 'max:255'],
                'email' => ['required', 'email', Rule::unique(User::class)->ignore($profile?->getKey())],
                'password' => self::passwordRules(),
            ], [
                'email.unique' => 'An account with this email address already exists.',
            ])->validate();

            $attributes = [
                'email' => $invite->email,
                'email_verified_at' => now(),
                'password' => $input['password'],
                'approved' => true,
            ];

            if ($profile instanceof User) {
                $profile->update($attributes);
                $user = $profile;
            } else {
                $user = User::create([
                    ...$attributes,
                    'name' => $input['name'],
                ]);
            }

            $invite->update([
                'user_id' => $user->getKey(),
                'accepted_at' => now(),
            ]);

            Invite::query()
                ->pending()
                ->whereKeyNot($invite->getKey())
                ->where(fn ($query) => $query->where('email', $invite->email)->orWhere('user_id', $user->getKey()))
                ->update(['revoked_at' => now()]);

            return $user;
        });
    }
}
