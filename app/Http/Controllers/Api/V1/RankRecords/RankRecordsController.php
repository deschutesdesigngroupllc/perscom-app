<?php

namespace App\Http\Controllers\Api\V1\RankRecords;

use App\Http\Requests\Api\RankRecordRequest;
use App\Models\RankRecord;
use App\Policies\RankRecordsPolicy;
use Orion\Http\Controllers\Controller;

class RankRecordsController extends Controller
{
    /**
     * @var string
     */
    protected $model = RankRecord::class;

    /**
     * @var string
     */
    protected $request = RankRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = RankRecordsPolicy::class;

    /**
     * @return string[]
     */
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

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'rank_id', 'rank.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'type', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'user_id', 'rank_id', 'document_id', 'author_id', 'text', 'type', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'rank_id', 'rank.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'type', 'created_at', 'updated_at', 'deleted_at'];
    }
}
