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
}
