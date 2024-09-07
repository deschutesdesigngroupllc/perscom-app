<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Categories;

use App\Http\Requests\Api\CategoryRequest;
use App\Models\Category;
use App\Policies\CategoryPolicy;
use Orion\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    protected $model = Category::class;

    protected $request = CategoryRequest::class;

    protected $policy = CategoryPolicy::class;

    public function includes(): array
    {
        return ['awards', 'documents', 'forms', 'qualifications', 'ranks'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'resource', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'resource', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'resource', 'created_at', 'updated_at'];
    }
}
