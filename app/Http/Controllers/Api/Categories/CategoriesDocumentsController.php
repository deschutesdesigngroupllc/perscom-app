<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\DocumentRequest;
use App\Models\Category;
use Orion\Http\Controllers\RelationController;

class CategoriesDocumentsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = Category::class;

    protected $request = DocumentRequest::class;

    protected $relation = 'documents';

    protected $pivotFillable = ['order'];

    public function includes(): array
    {
        return ['image'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'content', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'content', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'content', 'created_at', 'updated_at'];
    }
}
