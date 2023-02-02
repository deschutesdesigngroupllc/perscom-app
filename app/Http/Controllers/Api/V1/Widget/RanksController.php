<?php

namespace App\Http\Controllers\Api\V1\Widget;

use App\Http\Resources\Api\Widget\RankResource;
use App\Models\Rank;
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
    protected $resource = RankResource::class;
}
