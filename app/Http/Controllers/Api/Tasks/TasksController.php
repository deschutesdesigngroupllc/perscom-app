<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Requests\Api\TaskRequest;
use App\Models\Task;
use App\Policies\TaskPolicy;
use Orion\Http\Controllers\Controller;

class TasksController extends Controller
{
    protected $model = Task::class;

    protected $request = TaskRequest::class;

    protected $policy = TaskPolicy::class;

    public function includes(): array
    {
        return ['form', 'users'];
    }

    public function sortableBy(): array
    {
        return ['id', 'title', 'description', 'instructions', 'form_id', 'form.*', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'title', 'description', 'instructions', 'form_id', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'title', 'description', 'instructions', 'form_id', 'form.*', 'created_at', 'updated_at', 'deleted_at'];
    }
}
