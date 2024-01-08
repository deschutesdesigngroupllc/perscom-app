<?php

namespace App\Http\Controllers\Api\V1\Statuses;

use App\Http\Requests\Api\StatusRequest;
use App\Models\Status;
use App\Policies\StatusPolicy;
use Orion\Http\Controllers\Controller;

class StatusesController extends Controller
{
    /**
     * @var string
     */
    protected $model = Status::class;

    /**
     * @var string
     */
    protected $request = StatusRequest::class;

    /**
     * @var string
     */
    protected $policy = StatusPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['submissions', 'users'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['name', 'text_color', 'bg_color'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'text_color', 'bg_color', 'created_at'];
    }
}
