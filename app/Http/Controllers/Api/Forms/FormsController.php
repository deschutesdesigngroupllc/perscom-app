<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Forms;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\FormRequest;
use App\Models\Form;
use Orion\Http\Controllers\Controller;

class FormsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Form::class;

    protected $request = FormRequest::class;

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
        return ['id', 'name', 'slug', 'success_message', 'submission_status_id', 'submission_status.*', 'is_public', 'description', 'instructions', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'slug', 'success_message', 'submission_status_id', 'is_public', 'description', 'instructions', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'slug', 'success_message', 'submission_status_id', 'submission_status.*', 'is_public', 'description', 'instructions', 'created_at', 'updated_at'];
    }
}
