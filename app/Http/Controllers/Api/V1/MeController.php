<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class MeController extends Controller
{
    /**
     * @var string
     */
    protected $model = User::class;

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

        $query->where('id', '=', Auth::guard('api')->id())->limit(1);

        return $query;
    }
}
