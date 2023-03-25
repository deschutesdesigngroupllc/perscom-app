<?php

namespace App\Http\Controllers\Api\V1\Roster;

use App\Models\Unit;
use App\Policies\UnitPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Orion\Http\Controllers\Controller;

class RosterController extends Controller
{
    /**
     * @var string
     */
    protected $model = Unit::class;

    /**
     * @var string
     */
    protected $policy = UnitPolicy::class;

    /**
     * @return int
     */
    public function limit(): int
    {
        return 0;
    }

    /**
     * The relations that are loaded by default together with a resource.
     *
     * @return array
     */
    public function alwaysIncludes(): array
    {
        return ['users', 'users.rank', 'users.rank.image', 'users.position', 'users.specialty', 'users.status'];
    }

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

        $query->with([
            'users' => function (HasMany $query) {
                $query->select([
                    'users.name',
                    'users.id',
                    'users.unit_id',
                    'users.position_id',
                    'users.specialty_id',
                    'users.status_id',
                    'users.rank_id',
                ])
                    ->leftJoin('ranks', 'ranks.id', '=', 'users.rank_id')
                    ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
                    ->leftJoin('specialties', 'specialties.id', '=', 'users.specialty_id')
                    ->orderBy('ranks.order')
                    ->orderBy('positions.order')
                    ->orderBy('specialties.order')
                    ->orderBy('users.name');
            },
        ]);

        return $query;
    }
}
