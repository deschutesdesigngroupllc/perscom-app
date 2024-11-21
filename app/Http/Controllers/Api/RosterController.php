<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class RosterController extends Controller
{
    use AuthorizesRequests;

    protected $model = Group::class;

    public function includes(): array
    {
        return ['units.*'];
    }

    public function alwaysIncludes(): array
    {
        return [
            'image',
            'units',
            'units.image',
            'units.users',
            'units.users.position',
            'units.users.rank',
            'units.users.rank.image',
            'units.users.specialty',
            'units.users.status',
        ];
    }

    protected function buildFetchQuery(Request $request, array $requestedRelations): Builder
    {
        $query = parent::buildFetchQuery($request, $requestedRelations);

        /** @var Group|Builder $query */
        $query->orderForRoster();

        return $query;
    }
}
