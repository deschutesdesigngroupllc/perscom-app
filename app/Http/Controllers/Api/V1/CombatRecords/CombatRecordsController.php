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

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'user_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at', 'deleted_at'];
    }
}
