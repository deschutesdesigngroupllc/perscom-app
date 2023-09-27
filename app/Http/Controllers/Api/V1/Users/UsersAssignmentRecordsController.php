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
            'position',
            'specialty',
            'unit',
        ];
    }
}
