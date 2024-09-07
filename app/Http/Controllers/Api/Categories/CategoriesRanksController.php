<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Categories;

use App\Http\Requests\Api\RankRequest;
use App\Models\Category;
use App\Policies\RankPolicy;
use Orion\Http\Controllers\RelationController;

class CategoriesRanksController extends RelationController
{
    protected $model = Category::class;

    protected $request = RankRequest::class;

    protected $policy = RankPolicy::class;

    protected $relation = 'ranks';

    /**
     * @var string[]
     */
    protected $pivotFillable = ['order'];

    public function includes(): array
    {
        return ['image'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'abbreviation', 'paygrade', 'order', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'abbreviation', 'paygrade', 'order', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'abbreviation', 'paygrade', 'order', 'created_at', 'updated_at'];
    }
}
