<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Widgets;

use App\Models\Rank;
use Illuminate\Http\Response;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class WidgetRanksController extends Controller
{
    protected $model = Rank::class;

    protected function afterIndex(Request $request, $entities): Response
    {
        return response()->view('widgets.ranks');
    }
}
