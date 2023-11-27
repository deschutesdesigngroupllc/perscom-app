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
            'document',
            'rank',
            'rank.image',
            'user',
        ];
    }
}
