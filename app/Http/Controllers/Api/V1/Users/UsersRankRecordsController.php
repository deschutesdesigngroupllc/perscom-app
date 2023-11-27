<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\RankRecordRequest;
use App\Models\User;
use App\Policies\RankRecordsPolicy;
use Orion\Http\Controllers\RelationController;

class UsersRankRecordsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = RankRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = RankRecordsPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'rank_records';

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return [
            'author',
            'document',
            'rank',
            'rank.image',
        ];
    }
}
