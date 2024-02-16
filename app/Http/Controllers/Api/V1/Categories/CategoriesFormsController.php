<?php

namespace App\Http\Controllers\Api\V1\Categories;

use App\Http\Requests\Api\FormRequest;
use App\Models\Category;
use App\Policies\FormPolicy;
use Orion\Http\Controllers\RelationController;

class CategoriesFormsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Category::class;

    /**
     * @var string
     */
    protected $request = FormRequest::class;

    /**
     * @var string
     */
    protected $policy = FormPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'forms';

    /**
     * @return string[]
     */
    public function exposedScopes(): array
    {
        return ['tags'];
    }

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['fields', 'submissions', 'submissions.*', 'tags'];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'slug', 'success_message', 'submission_status', 'submission_status.*', 'is_public', 'description', 'instructions', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'slug', 'success_message', 'submission_status', 'is_public', 'description', 'instructions', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'slug', 'success_message', 'submission_status', 'submission_status.*', 'is_public', 'description', 'instructions', 'created_at', 'updated_at'];
    }
}
