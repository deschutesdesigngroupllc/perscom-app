<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\AssignmentRecordRequest;
use App\Models\User;
use App\Policies\AssignmentRecordsPolicy;
use Orion\Http\Controllers\RelationController;

class UsersAssignmentRecordsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = AssignmentRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = AssignmentRecordsPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'assignment_records';

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
