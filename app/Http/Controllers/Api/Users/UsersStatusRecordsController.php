<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\StatusRequest;
use App\Models\User;
use Orion\Http\Controllers\RelationController;

class UsersStatusRecordsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = User::class;

    protected $request = StatusRequest::class;

    protected $relation = 'statuses';

    protected $pivotFillable = ['text'];

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['record'];
    }
}
