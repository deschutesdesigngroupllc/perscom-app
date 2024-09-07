<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Requests\Api\RankRequest;
use App\Models\User;
use App\Policies\RankPolicy;
use Orion\Http\Controllers\RelationController;

class UsersRankController extends RelationController
{
    protected $model = User::class;

    protected $request = RankRequest::class;

    protected $policy = RankPolicy::class;

    protected $relation = 'rank';

    public function includes(): array
    {
        return ['image'];
    }
}
