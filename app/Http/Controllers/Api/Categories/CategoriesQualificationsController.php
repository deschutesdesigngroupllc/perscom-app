<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Categories;

use App\Http\Requests\Api\QualificationRequest;
use App\Models\Category;
use App\Policies\QualificationPolicy;
use Orion\Http\Controllers\RelationController;

class CategoriesQualificationsController extends RelationController
{
    protected $model = Category::class;

    protected $request = QualificationRequest::class;

    protected $policy = QualificationPolicy::class;

    protected $relation = 'qualifications';

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
