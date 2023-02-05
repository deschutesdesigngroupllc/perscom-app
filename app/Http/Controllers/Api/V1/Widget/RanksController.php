<?php

namespace App\Http\Controllers\Api\V1\Widget;

use App\Http\Resources\Api\Widget\RankResource;
use App\Models\Rank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Orion\Http\Controllers\Controller;

class RanksController extends Controller
{
    /**
     * @var string
     */
    protected $model = Rank::class;

    /**
     * @var string
     */
    protected $resource = RankResource::class;

    /**
     * Builds Eloquent query for fetching entities in index method.
     *
     * @param Request $request
     * @param array $requestedRelations
     * @return Builder
     */
    protected function buildIndexFetchQuery(Request $request, array $requestedRelations): Builder
    {
        $query = parent::buildIndexFetchQuery($request, $requestedRelations);

        $query->ordered();

        return $query;
    }
}
