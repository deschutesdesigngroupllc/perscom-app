<?php

namespace App\Http\Controllers\Api\V1\Widget;

use App\Http\Resources\Api\Widget\AwardResource;
use App\Models\Award;
use Orion\Http\Controllers\Controller;

class AwardsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Award::class;

    /**
     * @var string
     */
    protected $resource = AwardResource::class;
}
