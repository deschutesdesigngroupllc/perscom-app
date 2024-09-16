<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\TaskRequest;
use App\Models\User;
use Orion\Http\Controllers\RelationController;

class UsersTasksController extends RelationController
{
    use AuthorizesRequests;

    protected $model = User::class;

    protected $request = TaskRequest::class;

    protected $relation = 'tasks';

    protected $pivotFillable = ['assigned_by_id', 'assigned_at', 'due_at', 'completed_at', 'expires_at'];

    public function includes(): array
    {
        return [
            'assignment',
            'attachments',
        ];
    }
}
