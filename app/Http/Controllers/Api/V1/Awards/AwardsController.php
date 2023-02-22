<?php

namespace App\Http\Controllers\Api\V1\Awards;

use App\Models\Award;
use App\Policies\AwardPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Orion\Http\Controllers\Controller;

class AwardsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Award::class;

    /**
     * @var string
     */
    protected $policy = AwardPolicy::class;

    /**
     * Builds Eloquent query for fetching entities in index method.
     *
     * @param  Request  $request
     * @param  array  $requestedRelations
     * @return Builder
     */
    protected function buildIndexFetchQuery(Request $request, array $requestedRelations): Builder
    {
        $query = parent::buildIndexFetchQuery($request, $requestedRelations);

        $query->ordered();

        return $query;
    }
}
