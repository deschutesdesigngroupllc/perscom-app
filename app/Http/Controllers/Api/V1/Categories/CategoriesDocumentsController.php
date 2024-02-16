<?php

namespace App\Http\Controllers\Api\V1\Categories;

use App\Http\Requests\Api\DocumentRequest;
use App\Models\Category;
use App\Policies\DocumentPolicy;
use Orion\Http\Controllers\RelationController;

class CategoriesDocumentsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Category::class;

    /**
     * @var string
     */
    protected $request = DocumentRequest::class;

    /**
     * @var string
     */
    protected $policy = DocumentPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'documents';

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
        return ['id', 'name', 'description', 'content', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'content', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'content', 'created_at', 'updated_at'];
    }
}
