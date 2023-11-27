<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\CombatRecordRequest;
use App\Models\User;
use App\Policies\CombatRecordsPolicy;
use Orion\Http\Controllers\RelationController;

class UsersCombatRecordsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = CombatRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = CombatRecordsPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'combat_records';

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return [
            'author',
            'document',
        ];
    }
}
