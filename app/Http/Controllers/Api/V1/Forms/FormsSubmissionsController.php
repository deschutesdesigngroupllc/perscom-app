<?php

namespace App\Http\Controllers\Api\V1\Forms;

use App\Http\Requests\Api\SubmissionRequest;
use App\Models\Form;
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
    protected $relation = 'submissions';

    /**
     * @return string[]
     */
    public function alwaysIncludes(): array
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
        return ['id', 'form_id', 'user_id', 'created_at', 'tags'];
    }
}
