<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ContextServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $requestId = Str::uuid()->toString();
        $traceId = Str::uuid()->toString();

        Context::add('url', request()->url());
        Context::add('request_id', $requestId);
        Context::add('trace_id', $traceId);

        FilamentView::registerRenderHook(PanelsRenderHook::HEAD_START, fn (): string => sprintf('<meta name="perscom_request_id" content="%s"/>', $requestId));

        FilamentView::registerRenderHook(PanelsRenderHook::HEAD_START, fn (): string => sprintf('<meta name="perscom_trace_id" content="%s"/>', $traceId));

        Http::globalRequestMiddleware(fn ($request) => $request->withHeader(
            'X-Perscom-Trace-Id', $traceId
        ));
    }
}
