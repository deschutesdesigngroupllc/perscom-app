<?php

namespace App\Http\Controllers\Api\V1\ServiceRecords;

use App\Http\Requests\Api\ServiceRecordRequest;
use App\Models\ServiceRecord;
use App\Policies\ServiceRecordsPolicy;
use Orion\Http\Controllers\Controller;

class ServiceRecordsController extends Controller
{
    /**
     * @var string
     */
    protected $model = ServiceRecord::class;

    /**
     * @var string
     */
    protected $request = ServiceRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = ServiceRecordsPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return [
            'author',
            'document',
            'user',
        ];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'user_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }
}
