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
            'award_records',
            'combat_records',
            'position',
            'qualification_records',
            'rank',
            'rank_records',
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
        return ['name', 'email'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return [
            'id',
            'name',
            'position_id',
            'rank_id',
            'specialty_id',
            'status_id',
            'unit_id',
            'created_at',
        ];
    }
}
