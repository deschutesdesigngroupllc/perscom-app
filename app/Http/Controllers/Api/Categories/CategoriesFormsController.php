<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\FormRequest;
use App\Models\Category;
use Orion\Http\Controllers\RelationController;

class CategoriesFormsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = Category::class;

    protected $request = FormRequest::class;

    protected $relation = 'forms';

    protected $pivotFillable = ['order'];

    /**
     * @return array<int, string>
     */
    public function exposedScopes(): array
    {
        return ['tags'];
    }

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['fields', 'submissions', 'submissions.*', 'tags'];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'slug', 'success_message', 'submission_status', 'submission_status.*', 'is_public', 'description', 'instructions', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'slug', 'success_message', 'submission_status', 'is_public', 'description', 'instructions', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'slug', 'success_message', 'submission_status', 'submission_status.*', 'is_public', 'description', 'instructions', 'created_at', 'updated_at'];
    }
}
