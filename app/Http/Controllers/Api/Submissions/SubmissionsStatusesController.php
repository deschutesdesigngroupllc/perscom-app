<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Submissions;

use App\Http\Requests\Api\StatusRequest;
use App\Models\Submission;
use App\Policies\StatusPolicy;
use Orion\Http\Controllers\RelationController;

class SubmissionsStatusesController extends RelationController
{
    protected $model = Submission::class;

    protected $request = StatusRequest::class;

    protected $policy = StatusPolicy::class;

    protected $relation = 'statuses';

    /**
     * @var string[]
     */
    protected $pivotFillable = ['text'];

    public function includes(): array
    {
        return ['record'];
    }
}
