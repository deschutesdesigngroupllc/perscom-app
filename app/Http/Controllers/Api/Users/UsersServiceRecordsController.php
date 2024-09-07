<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Requests\Api\ServiceRecordRequest;
use App\Models\User;
use App\Policies\ServiceRecordPolicy;
use Orion\Http\Controllers\RelationController;

class UsersServiceRecordsController extends RelationController
{
    protected $model = User::class;

    protected $request = ServiceRecordRequest::class;

    protected $policy = ServiceRecordPolicy::class;

    protected $relation = 'service_records';

    public function includes(): array
    {
        return [
            'author',
            'author.*',
            'document',
        ];
    }

    public function sortableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'user_id', 'document_id', 'author_id', 'text', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'user_id', 'user.*', 'document_id', 'document.*', 'author_id', 'author.*', 'text', 'created_at', 'updated_at'];
    }
}
