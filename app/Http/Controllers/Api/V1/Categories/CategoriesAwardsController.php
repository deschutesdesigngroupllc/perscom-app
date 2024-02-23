<?php

namespace App\Http\Controllers\Api\V1\Categories;

use App\Http\Requests\Api\AwardRequest;
use App\Models\Category;
use App\Policies\AwardPolicy;
use Orion\Http\Controllers\RelationController;

class CategoriesAwardsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Category::class;

    /**
     * @var string
     */
    protected $request = AwardRequest::class;

    /**
     * @var string
     */
    protected $policy = AwardPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'awards';

    /**
     * @var string[]
     */
    protected $pivotFillable = ['order'];

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
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }
}
