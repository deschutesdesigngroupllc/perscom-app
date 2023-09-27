<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Requests\Api\TaskRequest;
use App\Models\Task;
use App\Policies\TaskPolicy;
use Orion\Http\Controllers\Controller;

class TasksController extends Controller
{
    /**
     * @var string
     */
    protected $model = Task::class;

    /**
     * @var string
     */
    protected $request = TaskRequest::class;

    /**
     * @var string
     */
    protected $policy = TaskPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['form', 'users'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['title', 'form_id'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'title', 'form_id', 'created_at'];
    }
}
