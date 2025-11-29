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

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['submissions', 'users'];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'color', 'icon', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'color', 'icon', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'color', 'icon', 'order', 'created_at', 'updated_at'];
    }
}
