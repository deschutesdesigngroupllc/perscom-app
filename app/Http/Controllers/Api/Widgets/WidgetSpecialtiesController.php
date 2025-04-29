<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Widgets;

use App\Models\Specialty;
use Illuminate\Http\Response;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class WidgetSpecialtiesController extends Controller
{
    protected $model = Specialty::class;

    protected function afterIndex(Request $request, $entities): Response
    {
        return response()->view('widgets.specialties');
    }
}
