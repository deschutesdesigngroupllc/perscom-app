<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\ServiceRecordRequest;
use App\Models\User;
use App\Policies\ServiceRecordsPolicy;
use Orion\Http\Controllers\RelationController;

class UsersServiceRecordsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = ServiceRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = ServiceRecordsPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'service_records';
}
