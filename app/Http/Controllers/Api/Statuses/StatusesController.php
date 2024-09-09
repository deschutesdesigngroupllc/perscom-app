<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Statuses;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\StatusRequest;
use App\Models\Status;
use Orion\Http\Controllers\Controller;

class StatusesController extends Controller
{
    use AuthorizesRequests;

    protected $model = Status::class;

    protected $request = StatusRequest::class;

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
