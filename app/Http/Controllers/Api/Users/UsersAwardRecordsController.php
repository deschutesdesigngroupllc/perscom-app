<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Requests\Api\AwardRecordRequest;
use App\Models\User;
use App\Policies\AwardRecordsPolicy;
use Orion\Http\Controllers\RelationController;

class UsersAwardRecordsController extends RelationController
{
    protected $model = User::class;

    protected $request = AwardRecordRequest::class;

    protected $policy = AwardRecordsPolicy::class;

    protected $relation = 'award_records';

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

    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'award_id', 'award.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'user_id', 'award_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'award_id', 'award.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }
}
