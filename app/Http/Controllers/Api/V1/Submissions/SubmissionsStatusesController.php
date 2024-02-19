<?php

namespace App\Http\Controllers\Api\V1\Submissions;

use App\Http\Requests\Api\StatusRequest;
use App\Models\Submission;
use App\Policies\StatusPolicy;
use Orion\Http\Controllers\RelationController;

class SubmissionsStatusesController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Submission::class;

    /**
     * @var string
     */
    protected $request = StatusRequest::class;

    /**
     * @var string
     */
    protected $policy = StatusPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'statuses';

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['record'];
    }
}
