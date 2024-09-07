<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Categories;

use App\Http\Requests\Api\FormRequest;
use App\Models\Category;
use App\Policies\FormPolicy;
use Orion\Http\Controllers\RelationController;

class CategoriesFormsController extends RelationController
{
    protected $model = Category::class;

    protected $request = FormRequest::class;

    protected $policy = FormPolicy::class;

    protected $relation = 'forms';

    /**
     * @var string[]
     */
    protected $pivotFillable = ['order'];

    public function exposedScopes(): array
    {
        return ['tags'];
    }

    public function includes(): array
    {
        return ['fields', 'submissions', 'submissions.*', 'tags'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'slug', 'success_message', 'submission_status', 'submission_status.*', 'is_public', 'description', 'instructions', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'slug', 'success_message', 'submission_status', 'is_public', 'description', 'instructions', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'slug', 'success_message', 'submission_status', 'submission_status.*', 'is_public', 'description', 'instructions', 'created_at', 'updated_at'];
    }
}
