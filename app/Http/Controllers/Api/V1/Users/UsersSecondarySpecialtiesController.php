<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\SpecialtyRequest;
use App\Models\User;
use App\Policies\SpecialtyPolicy;
use Orion\Http\Controllers\RelationController;

class UsersSecondarySpecialtiesController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = SpecialtyRequest::class;

    /**
     * @var string
     */
    protected $policy = SpecialtyPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'secondary_specialties';
}
