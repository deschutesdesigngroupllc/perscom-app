<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Forms;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\SubmissionRequest;
use App\Models\Form;
use Orion\Http\Controllers\RelationController;

class FormsSubmissionsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = Form::class;

    protected $request = SubmissionRequest::class;

    protected $relation = 'submissions';

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['form', 'user', 'statuses', 'statuses.record'];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'data', 'read_at', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'form_id', 'user_id', 'data', 'read_at', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'read_at', 'created_at', 'updated_at'];
    }
}
