<?php

namespace App\Http\Controllers\Api\V1\Widget;

use App\Http\Resources\Api\Widget\QualificationResource;
use App\Models\Qualification;
use Orion\Http\Controllers\Controller;

class QualificationsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Qualification::class;

    /**
     * @var string
     */
    protected $resource = QualificationResource::class;
}
