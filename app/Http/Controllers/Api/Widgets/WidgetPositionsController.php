<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Widgets;

use App\Models\Position;
use Illuminate\Http\Response;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class WidgetPositionsController extends Controller
{
    protected $model = Position::class;

    protected function afterIndex(Request $request, $entities): Response
    {
        return response()->view('widgets.positions');
    }
}
