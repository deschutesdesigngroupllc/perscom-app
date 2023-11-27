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
}
