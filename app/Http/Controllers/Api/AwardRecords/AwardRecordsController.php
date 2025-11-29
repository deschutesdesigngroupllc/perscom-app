<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\AwardRecords;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\AwardRecordRequest;
use App\Models\AwardRecord;
use Orion\Http\Controllers\Controller;

class AwardRecordsController extends Controller
{
    use AuthorizesRequests;

    protected $model = AwardRecord::class;

    protected $request = AwardRecordRequest::class;

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return [
            'attachments',
            'author',
            'author.*',
            'award',
            'award.image',
            'comments',
            'comments.*',
            'document',
            'user',
            'user.*',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'award_id', 'award.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'user_id', 'award_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'award_id', 'award.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }
}
