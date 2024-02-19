<?php

namespace App\Http\Controllers\Api\V1\Forms;

use App\Http\Requests\Api\SubmissionRequest;
use App\Models\Form;
use App\Policies\SubmissionPolicy;
use Orion\Http\Controllers\RelationController;

class FormsSubmissionsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Form::class;

    /**
     * @var string
     */
    protected $request = SubmissionRequest::class;

    /**
     * @var string
     */
    protected $policy = SubmissionPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'submissions';

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['form', 'user', 'statuses', 'statuses.record'];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'data', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'form_id', 'user_id', 'data', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'created_at', 'updated_at'];
    }
}
