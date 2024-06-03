<?php

namespace App\Http\Controllers\Api\V1\Submissions;

use App\Http\Requests\Api\SubmissionRequest;
use App\Models\Submission;
use App\Policies\SubmissionPolicy;
use Orion\Http\Controllers\Controller;

class SubmissionsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Submission::class;

    /**
     * @var string
     */
    protected $request = SubmissionRequest::class;

    /**
     * @var string
     */
    protected $policy = SubmissionPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['form', 'form.*', 'user', 'user.*', 'statuses', 'statuses.record'];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'data', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'form_id', 'user_id', 'data', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'created_at', 'updated_at', 'deleted_at'];
    }
}
