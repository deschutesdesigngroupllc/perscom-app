<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Requests\Api\AwardRecordRequest;
use App\Models\User;
use App\Policies\AwardRecordsPolicy;
use Orion\Http\Controllers\RelationController;

class UsersAwardRecordsController extends RelationController
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $request = AwardRecordRequest::class;

    /**
     * @var string
     */
    protected $policy = AwardRecordsPolicy::class;

    /**
     * @var string
     */
    protected $relation = 'award_records';

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return [
            'author',
            'author.*',
            'award',
            'award.image',
            'document',
        ];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'award_id', 'award.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'user_id', 'award_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'award_id', 'award.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }
}
