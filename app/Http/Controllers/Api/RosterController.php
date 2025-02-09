<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request as BaseRequest;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class RosterController extends Controller
{
    use AuthorizesRequests;

    protected $model = Group::class;

    public function __construct(protected BaseRequest $baseRequest)
    {
        parent::__construct();
    }

    public function includes(): array
    {
        return ['units.*'];
    }

    public function alwaysIncludes(): array
    {
        return match ($this->baseRequest->query('type')) {
            'manual' => [
                'image',
                'units',
                'units.image',
                'units.slots',
                'units.slots.assignment_records',
                'units.slots.assignment_records.user',
                'units.slots.assignment_records.user.position',
                'units.slots.assignment_records.user.rank',
                'units.slots.assignment_records.user.rank.image',
                'units.slots.assignment_records.user.specialty',
                'units.slots.assignment_records.user.status',
            ],
            default => [
                'image',
                'units',
                'units.image',
                'units.users',
                'units.users.position',
                'units.users.rank',
                'units.users.rank.image',
                'units.users.specialty',
                'units.users.status',
            ]
        };
    }

    protected function buildFetchQuery(Request $request, array $requestedRelations): Builder
    {
        $query = parent::buildFetchQuery($request, $requestedRelations);

        /** @var Group|Builder $query */
        match ($request->query('type')) {
            'manual' => $query->forManualRoster(),
            default => $query->forAutomaticRoster()
        };

        return $query;
    }
}
