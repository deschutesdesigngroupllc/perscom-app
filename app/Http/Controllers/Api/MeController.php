<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class MeController extends Controller
{
    protected $model = User::class;

    public function authorize(string $ability, $arguments = []): Response
    {
        return Response::allow();
    }

    protected function buildIndexFetchQuery(Request $request, array $requestedRelations): Builder
    {
        $query = parent::buildIndexFetchQuery($request, $requestedRelations);

        $query->where('id', '=', Auth::guard('api')->id())->first();

        return $query;
    }
}
