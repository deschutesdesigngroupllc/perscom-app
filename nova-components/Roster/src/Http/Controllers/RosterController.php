<?php

namespace Perscom\Roster\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Laravel\Nova\Http\Requests\NovaRequest;

class RosterController extends Controller
{
    /**
     * @return array
     */
    public function index(NovaRequest $request)
    {
        return [
            'new_unit_url' => route('nova.pages.create', [
                'resource' => \App\Nova\Unit::uriKey(),
            ], false),
            'units' => Unit::query()->ordered()->select(['name', 'id'])->with('users')->get(),
        ];
    }
}
