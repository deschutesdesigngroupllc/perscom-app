<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\RankRequest;
use App\Models\User;
use Orion\Http\Controllers\RelationController;

class UsersRankController extends RelationController
{
    use AuthorizesRequests;

    protected $model = User::class;

    protected $request = RankRequest::class;

    protected $relation = 'rank';

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['image'];
    }
}
