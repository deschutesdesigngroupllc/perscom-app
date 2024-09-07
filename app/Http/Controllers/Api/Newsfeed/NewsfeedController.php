<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Newsfeed;

use App\Http\Resources\NewsfeedResource;
use App\Models\Newsfeed;
use App\Policies\NewsfeedPolicy;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class NewsfeedController extends Controller
{
    protected $model = Newsfeed::class;

    protected $resource = NewsfeedResource::class;

    protected $policy = NewsfeedPolicy::class;

    /**
     * @throws BindingResolutionException
     */
    protected function runIndexFetchQuery(Request $request, Builder $query, int $paginationLimit): Paginator|Collection
    {
        $entities = parent::runIndexFetchQuery($request, $query, $paginationLimit);

        return tap($entities, function ($entities) {
            if ($entities instanceof LengthAwarePaginator) {
                $entities->setCollection($entities->getCollection()->filter(function (Newsfeed $item) {
                    return Gate::check('view', $item);
                }));
            }
        });
    }
}
