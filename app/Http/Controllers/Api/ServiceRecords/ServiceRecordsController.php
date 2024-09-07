<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\ServiceRecords;

use App\Http\Requests\Api\ServiceRecordRequest;
use App\Models\ServiceRecord;
use App\Policies\ServiceRecordPolicy;
use Orion\Http\Controllers\Controller;

class ServiceRecordsController extends Controller
{
    protected $model = ServiceRecord::class;

    protected $request = ServiceRecordRequest::class;

    protected $policy = ServiceRecordPolicy::class;

    public function includes(): array
    {
        return [
            'author',
            'author.*',
            'document',
            'user',
            'user.*',
        ];
    }

    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'user_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }
}
