<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Requests\Api\StatusRequest;
use App\Models\User;
use App\Policies\StatusPolicy;
use Orion\Http\Controllers\RelationController;

class UsersStatusRecordsController extends RelationController
{
    protected $model = User::class;

    protected $request = StatusRequest::class;

    protected $policy = StatusPolicy::class;

    protected $relation = 'statuses';

    /**
     * @var string[]
     */
    protected $pivotFillable = ['text'];

    public function includes(): array
    {
        return ['record'];
    }
}
