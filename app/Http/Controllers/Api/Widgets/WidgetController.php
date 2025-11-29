<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Widgets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class WidgetController extends Controller
{
    public function __invoke(Request $request, string $apiVersion, string $widget, ?string $resourceId = null): Response
    {
        Validator::make([
            'widget' => $request->route('widget'),
        ], [
            'widget' => ['in:awards,calendar,forms,positions,qualifications,ranks,roster,specialities'],
        ], [
            'widget.in' => 'The requested :attribute is invalid. Please provide a valid widget.',
        ])->validate();

        return response()->view('widgets.'.$widget, [
            'resourceId' => $resourceId,
        ]);
    }
}
