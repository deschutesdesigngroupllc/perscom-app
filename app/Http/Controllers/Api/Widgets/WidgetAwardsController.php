<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Widgets;

use App\Models\Award;
use Illuminate\Support\Facades\View;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WidgetAwardsController extends Controller
{
    protected $model = Award::class;

    protected function afterIndex(Request $request, $entities): StreamedResponse
    {
        return response()->stream(function () {
            echo View::make('widgets.awards');
        }, 200, [
            'Content-Type' => 'text/html',
        ]);
    }
}
