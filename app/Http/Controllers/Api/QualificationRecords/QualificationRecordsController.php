<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\QualificationRecords;

use App\Http\Requests\Api\QualificationRecordRequest;
use App\Models\QualificationRecord;
use App\Policies\QualificationRecordPolicy;
use Orion\Http\Controllers\Controller;

class QualificationRecordsController extends Controller
{
    protected $model = QualificationRecord::class;

    protected $request = QualificationRecordRequest::class;

    protected $policy = QualificationRecordPolicy::class;

    public function includes(): array
    {
        return [
            'author',
            'author.*',
            'document',
            'qualification',
            'qualification.image',
            'user',
            'user.*',
        ];
    }

    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'qualification_id', 'qualification.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'user_id', 'qualification_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'qualification_id', 'qualification.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }
}
