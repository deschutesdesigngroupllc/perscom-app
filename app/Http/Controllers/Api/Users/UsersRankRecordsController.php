<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\RankRecordRequest;
use App\Models\User;
use Orion\Http\Controllers\RelationController;

class UsersRankRecordsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = User::class;

    protected $request = RankRecordRequest::class;

    protected $relation = 'rank_records';

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return [
            'attachments',
            'author',
            'author.*',
            'comments',
            'comments.*',
            'document',
            'rank',
            'rank.image',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'rank_id', 'rank.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'user_id', 'rank_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'rank_id', 'rank.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }
}
