<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\TrainingRecordRequest;
use App\Models\User;
use Orion\Http\Controllers\RelationController;

class UsersTrainingRecordsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = User::class;

    protected $request = TrainingRecordRequest::class;

    protected $relation = 'training_records';

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
