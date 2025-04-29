<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Widgets;

use App\Models\Qualification;
use Illuminate\Http\Response;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class WidgetQualificationsController extends Controller
{
    protected $model = Qualification::class;

    protected function afterIndex(Request $request, $entities): Response
    {
        return response()->view('widgets.qualifications');
    }
}
