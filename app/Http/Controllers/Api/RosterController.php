<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Enums\RosterMode;
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
                'units.slots.users',
                'units.slots.users.position',
                'units.slots.users.rank',
                'units.slots.users.rank.image',
                'units.slots.users.specialty',
                'units.slots.users.status',
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
        match (RosterMode::tryFrom($request->query('type') ?? 'automatic')) {
            RosterMode::MANUAL => $query->forManualRoster(),
            default => $query->forAutomaticRoster()
        };

        return $query;
    }
}
