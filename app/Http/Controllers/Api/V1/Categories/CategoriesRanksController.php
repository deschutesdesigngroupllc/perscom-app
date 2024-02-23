<?php

namespace App\Http\Controllers\Api\V1\Categories;

use App\Http\Requests\Api\RankRequest;
use App\Models\Category;
use App\Policies\RankPolicy;
use Orion\Http\Controllers\RelationController;

class CategoriesRanksController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Category::class;

    /**
     * @var string
     */
    protected $request = RankRequest::class;

    /**
     * @var string
     */
    protected $policy = RankPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'ranks';

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
        return ['id', 'name', 'description', 'abbreviation', 'paygrade', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'abbreviation', 'paygrade', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'abbreviation', 'paygrade', 'order', 'created_at', 'updated_at'];
    }
}
