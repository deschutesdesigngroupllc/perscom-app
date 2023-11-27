<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\QualificationRecordRequest;
use App\Models\User;
use App\Policies\QualificationRecordsPolicy;
use Orion\Http\Controllers\RelationController;

class UsersQualificationRecordsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = QualificationRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = QualificationRecordsPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'qualification_records';

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return [
            'author',
            'document',
            'qualification',
            'qualification.image',
        ];
    }
}
