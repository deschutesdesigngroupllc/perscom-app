<?php

namespace App\Http\Controllers\Api\V1\Ranks;

use App\Http\Requests\Api\RankRequest;
use App\Models\Rank;
use App\Policies\RankPolicy;
use Orion\Http\Controllers\Controller;

class RanksController extends Controller
{
    /**
     * @var string
     */
    protected $model = Rank::class;

    /**
     * @var string
     */
    protected $request = RankRequest::class;

    /**
     * @var string
     */
    protected $policy = RankPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['image'];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'abbreviation', 'paygrade', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'abbreviation', 'paygrade', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'abbreviation', 'paygrade', 'order', 'created_at', 'updated_at'];
    }
}
