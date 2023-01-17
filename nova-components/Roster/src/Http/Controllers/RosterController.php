<?php

namespace Perscom\Roster\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class RosterController extends Controller
{
    /**
     * @return array
     */
    public function index(NovaRequest $request)
    {
        return Unit::query()->ordered()->select(['name', 'id'])->with([
            'users' => function (HasMany $query) {
                $query->without(['unit'])
                      ->select([
                          'users.name',
                          'users.email',
                          'users.id',
                          'users.unit_id',
                          'users.position_id',
                          'users.specialty_id',
                          'users.status_id',
                          'users.rank_id',
                      ])
                      ->join('positions', 'positions.id', '=', 'users.position_id')
                      ->join('specialties', 'specialties.id', '=', 'users.specialty_id')
                      ->join('ranks', 'ranks.id', '=', 'users.rank_id')
                      ->orderBy('ranks.order')
                      ->orderBy('positions.order')
                      ->orderBy('users.name');
            },
        ])->get();
    }
}
