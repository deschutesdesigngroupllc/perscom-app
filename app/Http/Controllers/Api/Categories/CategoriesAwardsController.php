<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\AwardRequest;
use App\Models\Category;
use Orion\Http\Controllers\RelationController;

class CategoriesAwardsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = Category::class;

    protected $request = AwardRequest::class;

    protected $relation = 'awards';

    protected $pivotFillable = ['order'];

    public function includes(): array
    {
        return ['image'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }
}
