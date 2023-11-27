<?php

namespace App\Http\Controllers\Api\V1\CombatRecords;

use App\Http\Requests\Api\CombatRecordRequest;
use App\Models\CombatRecord;
use App\Policies\CombatRecordsPolicy;
use Orion\Http\Controllers\Controller;

class CombatRecordsController extends Controller
{
    /**
     * @var string
     */
    protected $model = CombatRecord::class;

    /**
     * @var string
     */
    protected $request = CombatRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = CombatRecordsPolicy::class;

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
