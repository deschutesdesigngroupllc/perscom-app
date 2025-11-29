<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Submissions;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\StatusRequest;
use App\Models\Submission;
use Orion\Http\Controllers\RelationController;

class SubmissionsStatusesController extends RelationController
{
    use AuthorizesRequests;

    protected $model = Submission::class;

    protected $request = StatusRequest::class;

    protected $relation = 'statuses';

    protected $pivotFillable = ['text'];

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['record'];
    }
}
