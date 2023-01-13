<?php

namespace App\Http\Controllers\Api\V1\Forms;

use App\Http\Requests\SubmissionRequest;
use App\Models\Forms\Submission;
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
        return ['form', 'user'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['form_id', 'user_id'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'form_id', 'user_id', 'created_at'];
    }
}
