<?php

namespace App\Http\Controllers\Api\V1\AwardRecords;

use App\Http\Requests\Api\AwardRecordRequest;
use App\Models\AwardRecord;
use App\Policies\AwardRecordsPolicy;
use Orion\Http\Controllers\Controller;

class AwardRecordsController extends Controller
{
    /**
     * @var string
     */
    protected $model = AwardRecord::class;

    /**
     * @var string
     */
    protected $request = AwardRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = AwardRecordsPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return [
            'author',
            'award',
            'award.image',
            'document',
            'user',
        ];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'award_id', 'award.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'user_id', 'award_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'award_id', 'award.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }
}
