<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\TaskRequest;
use App\Models\User;
use App\Policies\TaskPolicy;
use Orion\Http\Controllers\RelationController;

class UsersTasksController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = TaskRequest::class;

    /**
     * @var string
     */
    protected $policy = TaskPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'tasks';

    /**
     * @var string[]
     */
    protected $pivotFillable = ['assigned_by_id', 'assigned_at', 'due_at', 'completed_at', 'expires_at'];

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['assignment'];
    }
}
