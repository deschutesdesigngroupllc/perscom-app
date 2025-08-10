<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Submissions;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\SubmissionRequest;
use App\Models\Submission;
use Orion\Http\Controllers\Controller;

class SubmissionsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Submission::class;

    protected $request = SubmissionRequest::class;

    public function includes(): array
    {
        return [
            'comments',
            'comments.*',
            'form',
            'form.*',
            'user',
            'user.*',
            'statuses',
            'statuses.record',
        ];
    }

    public function sortableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'data', 'read_at', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'form_id', 'user_id', 'data', 'read_at', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'read_at', 'created_at', 'updated_at'];
    }
}
