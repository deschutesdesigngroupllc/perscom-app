<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\RankRecords;

use App\Http\Requests\Api\RankRecordRequest;
use App\Models\RankRecord;
use App\Policies\RankRecordPolicy;
use Orion\Http\Controllers\Controller;

class RankRecordsController extends Controller
{
    protected $model = RankRecord::class;

    protected $request = RankRecordRequest::class;

    protected $policy = RankRecordPolicy::class;

    public function includes(): array
    {
        return [
            'author',
            'author.*',
            'document',
            'rank',
            'rank.image',
            'user',
            'user.*',
        ];
    }

    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'rank_id', 'rank.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'type', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'user_id', 'rank_id', 'document_id', 'author_id', 'text', 'type', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'rank_id', 'rank.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'type', 'created_at', 'updated_at', 'deleted_at'];
    }
}
