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

    public function alwaysIncludes(): array
    {
        return [
            'units',
            'units.users',
            'units.users.position',
            'units.users.rank',
            'units.users.rank.image',
            'units.users.status',
            'units.secondary_assignment_records',
            'units.secondary_assignment_records.position',
            'units.secondary_assignment_records.user',
            'units.secondary_assignment_records.user.rank',
            'units.secondary_assignment_records.user.rank.image',
            'units.secondary_assignment_records.user.status',
        ];
    }

    protected function buildIndexFetchQuery(Request $request, array $requestedRelations): Builder
    {
        $query = parent::buildIndexFetchQuery($request, $requestedRelations);

        /** @var Group $query */
        $query->orderForRoster();

        return $query;
    }
}
