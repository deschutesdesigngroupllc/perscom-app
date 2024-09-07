<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Requests\Api\TaskRequest;
use App\Models\User;
use App\Policies\TaskPolicy;
use Orion\Http\Controllers\RelationController;

class UsersTasksController extends RelationController
{
    protected $model = User::class;

    protected $request = TaskRequest::class;

    protected $policy = TaskPolicy::class;

    protected $relation = 'tasks';

    /**
     * @var string[]
     */
    protected $pivotFillable = ['assigned_by_id', 'assigned_at', 'due_at', 'completed_at', 'expires_at'];

    public function includes(): array
    {
        return ['assignment'];
    }
}
