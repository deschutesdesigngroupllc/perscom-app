<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\UserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class UsersController extends Controller
{
    use AuthorizesRequests;

    protected $model = User::class;

    protected $request = UserRequest::class;

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return [
            'assignment_records',
            'assignment_records.*',
            'attachments',
            'awards',
            'award_records',
            'award_records.*',
            'award_records.award.*',
            'combat_records',
            'combat_records.*',
            'credentials',
            'fields',
            'position',
            'primary_assignment_records',
            'primary_assignment_records.*',
            'qualifications',
            'qualification_records',
            'qualification_records.*',
            'rank',
            'rank.*',
            'rank_records',
            'rank_records.*',
            'secondary_assignment_records',
            'secondary_assignment_records.*',
            'service_records',
            'service_records.*',
            'specialty',
            'status',
            'unit',
            'unit.*',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return [
            'id',
            'name',
            'email',
            'email_verified_at',
            'phone_number',
            'position_id',
            'position.*',
            'rank_id',
            'rank.*',
            'specialty_id',
            'specialty.*',
            'status_id',
            'status.*',
            'unit_id',
            'unit.*',
            'unit_slot_id',
            'approved',
            'notes',
            'notes_updated_at',
            'profile_photo',
            'cover_photo',
            'last_seen_at',
            'facebook_user_id',
            'github_user_id',
            'google_user_id',
            'discord_user_id',
            'discord_private_channel_id',
            'updated_at',
            'created_at',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return [
            'id',
            'name',
            'email',
            'email_verified_at',
            'phone_number',
            'position_id',
            'position.*',
            'rank_id',
            'rank.*',
            'specialty_id',
            'specialty.*',
            'status_id',
            'status.*',
            'unit_id',
            'unit.*',
            'unit_slot_id',
            'approved',
            'notes',
            'notes_updated_at',
            'profile_photo',
            'cover_photo',
            'last_seen_at',
            'facebook_user_id',
            'github_user_id',
            'google_user_id',
            'discord_user_id',
            'discord_private_channel_id',
            'updated_at',
            'created_at',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return [
            'id',
            'name',
            'email',
            'email_verified_at',
            'phone_number',
            'position_id',
            'position.*',
            'rank_id',
            'rank.*',
            'specialty_id',
            'specialty.*',
            'status_id',
            'status.*',
            'unit_id',
            'unit.*',
            'unit_slot_id',
            'approved',
            'notes',
            'notes_updated_at',
            'profile_photo',
            'cover_photo',
            'last_seen_at',
            'facebook_user_id',
            'github_user_id',
            'google_user_id',
            'discord_user_id',
            'discord_private_channel_id',
            'updated_at',
            'created_at',
        ];
    }

    protected function beforeSave(Request $request, Model $entity): void
    {
        if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
            $path = $request->file('profile_photo')->storePublicly('profile-photos');

            $entity->forceFill([
                'profile_photo' => $path,
            ]);
        }

        if ($request->hasFile('cover_photo') && $request->file('cover_photo')->isValid()) {
            $path = $request->file('cover_photo')->storePublicly('cover-photos');

            $entity->forceFill([
                'cover_photo' => $path,
            ]);
        }
    }
}
