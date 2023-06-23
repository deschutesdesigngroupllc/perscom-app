<?php

namespace App\Http\Controllers\Api\V1\Newsfeed;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Orion\Http\Controllers\Controller;

class NewsfeedController extends Controller
{
    /**
     * @var string
     */
    protected $model = Activity::class;

    /**
     * @var string
     */
    protected $resource = ActivityResource::class;

    /**
     * The relations that are loaded by default together with a resource.
     */
    public function alwaysIncludes(): array
    {
        return ['causer', 'subject', 'subject.user'];
    }

    /**
     * Builds Eloquent query for fetching entities in index method.
     */
    protected function buildIndexFetchQuery(Request $request, array $requestedRelations): Builder
    {
        $query = parent::buildIndexFetchQuery($request, $requestedRelations);

        $query->where('log_name', 'newsfeed')->latest();

        return $query;
    }
}
