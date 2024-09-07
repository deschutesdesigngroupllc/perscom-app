<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Requests\Api\RankRecordRequest;
use App\Models\User;
use App\Policies\RankRecordPolicy;
use Orion\Http\Controllers\RelationController;

class UsersRankRecordsController extends RelationController
{
    protected $model = User::class;

    protected $request = RankRecordRequest::class;

    protected $policy = RankRecordPolicy::class;

    protected $relation = 'rank_records';

    public function includes(): array
    {
        return [
            'author',
            'author.*',
            'document',
            'rank',
            'rank.image',
        ];
    }

    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'rank_id', 'rank.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'user_id', 'rank_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'rank_id', 'rank.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }
}
