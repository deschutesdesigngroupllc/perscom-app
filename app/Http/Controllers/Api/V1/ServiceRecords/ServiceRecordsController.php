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
}
