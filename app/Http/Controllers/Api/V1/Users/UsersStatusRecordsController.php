<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\StatusRequest;
use App\Models\User;
use App\Policies\StatusPolicy;
use Orion\Http\Controllers\RelationController;

class UsersStatusRecordsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = StatusRequest::class;

    /**
     * @var string
     */
    protected $policy = StatusPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'statuses';

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['record'];
    }
}
