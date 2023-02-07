<?php

namespace App\Http\Controllers\Api\V1\Widget;

use App\Http\Resources\Api\Widget\AwardResource;
use App\Models\Award;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AwardsController extends WidgetController
{
    /**
     * @var string
     */
    protected $model = Award::class;

    /**
     * @var string
     */
    protected $resource = AwardResource::class;

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
