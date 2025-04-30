<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Widgets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WidgetController extends Controller
{
    public function __invoke(Request $request, string $apiVersion, string $widget): Response
    {
        return response()->view("widgets.$widget");
    }
}
