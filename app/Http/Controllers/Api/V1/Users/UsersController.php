<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\UserRequest;
use App\Models\User;
use App\Policies\UserPolicy;
use Orion\Http\Controllers\Controller;

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
            'assignment_records.position',
            'assignment_records.specialty',
            'assignment_records.unit',
            'award_records',
            'award_records.award',
            'award_records.award.image',
            'combat_records',
            'fields',
            'position',
            'qualification_records',
            'qualification_records.qualification',
            'qualification_records.qualification.image',
            'rank',
            'rank.image',
            'rank_records',
            'rank_records.rank',
            'rank_records.rank.image',
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
    public function searchableBy(): array
    {
        return ['name', 'email', 'approved', 'online'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'email', 'position_id', 'rank_id', 'specialty_id', 'status_id', 'unit_id', 'approved', 'online', 'created_at'];
    }
}
