<?php

namespace App\Http\Controllers\Api\V1\AssignmentRecords;

use App\Http\Requests\Api\AssignmentRecordRequest;
use App\Models\AssignmentRecord;
use App\Policies\AssignmentRecordsPolicy;
use Orion\Http\Controllers\Controller;

class AssignmentRecordsController extends Controller
{
    /**
     * @var string
     */
    protected $model = AssignmentRecord::class;

    /**
     * @var string
     */
    protected $request = AssignmentRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = AssignmentRecordsPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return [
            'author',
            'document',
            'position',
            'specialty',
            'status',
            'unit',
            'user',
        ];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'status_id', 'status.*', 'unit_id', 'unit.*', 'secondary_unit_ids', 'position_id', 'position.*', 'secondary_position_ids', 'specialty_id', 'specialty.*', 'secondary_specialty_ids', 'document_id', 'document.*', 'author_id', 'author.*', 'type', 'text', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'user_id', 'status_id', 'unit_id', 'secondary_unit_ids', 'position_id', 'secondary_position_ids', 'specialty_id', 'secondary_specialty_ids', 'document_id', 'author_id', 'type', 'text', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'status_id', 'status.*', 'unit_id', 'unit.*', 'secondary_unit_ids', 'position_id', 'position.*', 'secondary_position_ids', 'specialty_id', 'specialty.*', 'secondary_specialty_ids', 'document_id', 'document.*', 'author_id', 'author.*', 'type', 'text', 'created_at', 'updated_at'];
    }
}
