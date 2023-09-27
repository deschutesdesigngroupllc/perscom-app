<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\AwardRecordRequest;
use App\Models\User;
use App\Policies\AwardRecordsPolicy;
use Orion\Http\Controllers\RelationController;

class UsersAwardRecordsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = AwardRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = AwardRecordsPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'award_records';

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['award', 'award.image'];
    }
}
