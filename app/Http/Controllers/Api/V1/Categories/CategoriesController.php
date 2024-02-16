<?php

namespace App\Http\Controllers\Api\V1\Categories;

use App\Http\Requests\Api\CategoryRequest;
use App\Models\Category;
use App\Policies\CategoryPolicy;
use Orion\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    /**
     * @var string
     */
    protected $model = Category::class;

    /**
     * @var string
     */
    protected $request = CategoryRequest::class;

    /**
     * @var string
     */
    protected $policy = CategoryPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['awards', 'documents', 'forms', 'qualifications', 'ranks'];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'resource', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'resource', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'resource', 'created_at', 'updated_at'];
    }
}
