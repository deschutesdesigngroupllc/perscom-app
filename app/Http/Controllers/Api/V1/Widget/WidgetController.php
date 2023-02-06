<?php

namespace App\Http\Controllers\Api\V1\Widget;

use App\Services\Api\Widget\PaginatorService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Orion\Http\Controllers\Controller;
use Spatie\Url\Url;

class WidgetController extends Controller
{
    /**
     * Runs the given query for fetching entities in index method.
     *
     * @param \Orion\Http\Requests\Request $request
     * @param Builder                      $query
     * @param int                          $paginationLimit
     *
     * @return Paginator|Collection
     * @throws BindingResolutionException
     */
    protected function runIndexFetchQuery(Request $request, Builder $query, int $paginationLimit)
    {
        $path = optional($request->headers->get('X-Perscom-Widget'), function () use ($request) {
           return optional($request->headers->get('X-Perscom-Widget-Id'), function ($widgetId) use ($request) {
               return optional($request->headers->get('origin'), function ($origin) use ($widgetId) {
                   return Url::fromString($origin)->withPath($widgetId)->__toString();
               });
           });
        });

        $paginator = $query->paginate($paginationLimit);

        if ($path) {
            $paginator->withPath($path);
        }

        return $this->shouldPaginate($request, $paginationLimit) ? $paginator : $query->get();
    }
}
