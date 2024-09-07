<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Statuses;

use App\Http\Requests\Api\StatusRequest;
use App\Models\Status;
use App\Policies\StatusPolicy;
use Orion\Http\Controllers\Controller;

class StatusesController extends Controller
{
    protected $model = Status::class;

    protected $request = StatusRequest::class;

    protected $policy = StatusPolicy::class;

    public function includes(): array
    {
        return ['submissions', 'users'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'color', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'color', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'color', 'created_at', 'updated_at', 'deleted_at'];
    }
}
