<?php

namespace App\Http\Controllers\Api\V1\Awards;

use App\Http\Requests\Api\AwardRequest;
use App\Models\Award;
use App\Policies\AwardPolicy;
use Orion\Http\Controllers\Controller;

class AwardsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Award::class;

    /**
     * @var string
     */
    protected $request = AwardRequest::class;

    /**
     * @var string
     */
    protected $policy = AwardPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['image'];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at', 'deleted_at'];
    }
}
