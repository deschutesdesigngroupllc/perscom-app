<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Widgets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WidgetRequest;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class WidgetController extends Controller
{
    public function __invoke(WidgetRequest $request, string $apiVersion, string $widget, ?string $resourceId = null): Response
    {
        FilamentColor::register([
            'primary' => Color::Blue,
        ]);

        URL::useOrigin(config('tenancy.enabled')
            ? tenant()->url
            : config('app.url'));

        return response()->view('widgets.'.$widget, [
            'resourceId' => $resourceId,
        ]);
    }
}
