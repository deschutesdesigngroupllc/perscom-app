<?php

namespace App\Http\Controllers\Api\V1\Widget;

use App\Http\Resources\Api\Widget\QualificationResource;
use App\Models\Qualification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QualificationsController extends WidgetController
{
    /**
     * @var string
     */
    protected $model = Qualification::class;

    /**
     * @var string
     */
    protected $resource = QualificationResource::class;

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
