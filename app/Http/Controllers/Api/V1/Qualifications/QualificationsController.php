<?php

namespace App\Http\Controllers\Api\V1\Qualifications;

use App\Http\Requests\Api\QualificationRequest;
use App\Models\Qualification;
use App\Policies\QualificationPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Orion\Http\Controllers\Controller;

class QualificationsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Qualification::class;

    /**
     * @var string
     */
    protected $request = QualificationRequest::class;

    /**
     * @var string
     */
    protected $policy = QualificationPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['image'];
    }

    /**
     * Builds Eloquent query for fetching entities in index method.
     *
     * @param  Request  $request
     * @param  array  $requestedRelations
     * @return Builder
     */
    protected function buildIndexFetchQuery(
        Request $request,
        array $requestedRelations
    ): Builder {
        $query = parent::buildIndexFetchQuery($request, $requestedRelations);

        $query->ordered();

        return $query;
    }
}
