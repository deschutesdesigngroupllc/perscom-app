<?php

namespace App\Http\Controllers\Api\V1\QualificationRecords;

use App\Http\Requests\Api\QualificationRecordRequest;
use App\Models\QualificationRecord;
use App\Policies\QualificationRecordsPolicy;
use Orion\Http\Controllers\Controller;

class QualificationRecordsController extends Controller
{
    /**
     * @var string
     */
    protected $model = QualificationRecord::class;

    /**
     * @var string
     */
    protected $request = QualificationRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = QualificationRecordsPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return [
            'author',
            'document',
            'qualification',
            'qualification.image',
            'user',
        ];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'qualification_id', 'qualification.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'user_id', 'qualification_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'qualification_id', 'qualification.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }
}
