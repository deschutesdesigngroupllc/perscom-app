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

    public function includes(): array
    {
        return ['form', 'user', 'statuses', 'statuses.record'];
    }

    public function sortableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'data', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'form_id', 'user_id', 'data', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'created_at', 'updated_at'];
    }
}
