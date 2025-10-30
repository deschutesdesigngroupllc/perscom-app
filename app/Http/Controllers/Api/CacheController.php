<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\Tenant\PurgeApiCache;
use App\Services\ApiCacheService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Spatie\Health\ResultStores\ResultStore;
use Symfony\Component\HttpFoundation\JsonResponse;

class CacheController extends Controller
{
    use AuthorizesRequests;

    /**
     * @throws AuthorizationException
     * @throws BindingResolutionException
     */
    public function __invoke(Request $request, ResultStore $resultStore): JsonResponse
    {
        $this->authorize('clear:cache');

        $apiService = new ApiCacheService;

        PurgeApiCache::dispatch(
            tags: $apiService->getTenantCacheTag(),
            event: 'manual'
        );

        return response()->json([
            'status' => 'okay',
        ]);
    }
}
