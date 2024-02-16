<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\UserRequest;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Database\Eloquent\Model;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class UsersController extends Controller
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = UserRequest::class;

    /**
     * @var string
     */
    protected $policy = UserPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return [
            'assignment_records',
            'assignment_records.*',
            'award_records',
            'award_records.*',
            'award_records.award.*',
            'combat_records',
            'fields',
            'position',
            'qualification_records',
            'qualification_records.*',
            'rank',
            'rank.*',
            'rank_records',
            'rank_records.*',
            'rank_records.rank.*',
            'secondary_positions',
            'secondary_specialties',
            'secondary_units',
            'service_records',
            'specialty',
            'status',
            'unit',
        ];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return [
            'id',
            'name',
            'email',
            'email_verified_at',
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
            'approved',
            'last_seen_at',
            'updated_at',
            'created_at',
        ];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return [
            'id',
            'name',
            'email',
            'email_verified_at',
            'position_id',
            'rank_id',
            'specialty_id',
            'status_id',
            'unit_id',
            'approved',
            'last_seen_at',
            'updated_at',
            'created_at',
        ];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return [
            'id',
            'name',
            'email',
            'email_verified_at',
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
            'approved',
            'last_seen_at',
            'updated_at',
            'created_at',
        ];
    }

    public function beforeSave(Request $request, Model $entity): void
    {
        if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
            $path = $request->file('profile_photo')->store('profile-photos', 's3_public');

            $entity->forceFill([
                'profile_photo' => $path,
            ]);
        }

        if ($request->hasFile('cover_photo') && $request->file('cover_photo')->isValid()) {
            $path = $request->file('cover_photo')->store('cover-photos', 's3_public');

            $entity->forceFill([
                'cover_photo' => $path,
            ]);
        }
    }
}
