<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Models\User;
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
    protected $relation = 'qualification_records';

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['qualification'];
    }
}
