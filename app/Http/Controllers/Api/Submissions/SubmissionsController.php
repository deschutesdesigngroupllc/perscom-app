<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Submissions;

use App\Http\Requests\Api\SubmissionRequest;
use App\Models\Submission;
use App\Policies\SubmissionPolicy;
use Orion\Http\Controllers\Controller;

class SubmissionsController extends Controller
{
    protected $model = Submission::class;

    protected $request = SubmissionRequest::class;

    protected $policy = SubmissionPolicy::class;

    public function includes(): array
    {
        return ['form', 'form.*', 'user', 'user.*', 'statuses', 'statuses.record'];
    }

    public function sortableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'data', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'form_id', 'user_id', 'data', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'created_at', 'updated_at', 'deleted_at'];
    }
}
