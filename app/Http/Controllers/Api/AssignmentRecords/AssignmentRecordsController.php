<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\AssignmentRecords;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\AssignmentRecordRequest;
use App\Models\AssignmentRecord;
use Orion\Http\Controllers\Controller;

class AssignmentRecordsController extends Controller
{
    use AuthorizesRequests;

    protected $model = AssignmentRecord::class;

    protected $request = AssignmentRecordRequest::class;

    public function includes(): array
    {
        return [
            'author',
            'author.*',
            'document',
            'position',
            'specialty',
            'status',
            'unit',
            'user',
            'user.*',
        ];
    }

    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'status_id', 'status.*', 'unit_id', 'unit.*', 'position_id', 'position.*', 'specialty_id', 'specialty.*', 'document_id', 'document.*', 'author_id', 'author.*', 'type', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'user_id', 'status_id', 'unit_id', 'position_id', 'specialty_id', 'document_id', 'author_id', 'type', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'status_id', 'status.*', 'unit_id', 'unit.*', 'position_id', 'position.*', 'specialty_id', 'specialty.*', 'document_id', 'document.*', 'author_id', 'author.*', 'type', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }
}
