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
}
