<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\TrainingRecords;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\TrainingRecordRequest;
use App\Models\TrainingRecord;
use Orion\Http\Controllers\Controller;

class TrainingRecordsController extends Controller
{
    use AuthorizesRequests;

    protected $model = TrainingRecord::class;

    protected $request = TrainingRecordRequest::class;

    public function includes(): array
    {
        return [
            'attachments',
            'author',
            'author.*',
            'comments',
            'comments.*',
            'competencies',
            'credentials',
            'credentials.*',
            'document',
            'event',
            'event.*',
            'instructor',
            'instructor.*',
            'user',
            'user.*',
        ];
    }

    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'instructor_id', 'instructor.*', 'event_id', 'event.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'user_id', 'instructor_id', 'event_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'instructor_id', 'instructor.*', 'event_id', 'event.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }
}
