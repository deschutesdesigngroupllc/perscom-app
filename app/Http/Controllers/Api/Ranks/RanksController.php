<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ranks;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\RankRequest;
use App\Models\Rank;
use Orion\Http\Controllers\Controller;

class RanksController extends Controller
{
    use AuthorizesRequests;

    protected $model = Rank::class;

    protected $request = RankRequest::class;

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['image'];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'abbreviation', 'paygrade', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'abbreviation', 'paygrade', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'abbreviation', 'paygrade', 'order', 'created_at', 'updated_at'];
    }
}
