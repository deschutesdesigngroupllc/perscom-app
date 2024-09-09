<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\CategoryRequest;
use App\Models\Category;
use Orion\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    use AuthorizesRequests;

    protected $model = Category::class;

    protected $request = CategoryRequest::class;

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
