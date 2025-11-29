<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Awards;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\AwardRequest;
use App\Models\Award;
use Orion\Http\Controllers\Controller;

class AwardsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Award::class;

    protected $request = AwardRequest::class;

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['image'];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }
}
