<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\RankRequest;
use App\Models\User;
use App\Policies\RankPolicy;
use Orion\Http\Controllers\RelationController;

class UsersRankController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = RankRequest::class;

    /**
     * @var string
     */
    protected $policy = RankPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'rank';

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['image'];
    }
}
