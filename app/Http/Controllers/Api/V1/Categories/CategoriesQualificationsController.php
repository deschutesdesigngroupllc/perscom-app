<?php

namespace App\Http\Controllers\Api\V1\Categories;

use App\Http\Requests\Api\QualificationRequest;
use App\Models\Category;
use App\Policies\QualificationPolicy;
use Orion\Http\Controllers\RelationController;

class CategoriesQualificationsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Category::class;

    /**
     * @var string
     */
    protected $request = QualificationRequest::class;

    /**
     * @var string
     */
    protected $policy = QualificationPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'qualifications';

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
    public function searchableBy(): array
    {
        return ['name'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'created_at'];
    }
}
