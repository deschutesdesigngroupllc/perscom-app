<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Services\ApiCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Orion\Http\Resources\CollectionResource;

class ApiResourceCollection extends CollectionResource
{
    public function withResponse(Request $request, JsonResponse $response): void
    {
        if (! $this->isCacheableRequest($request)) {
            return;
        }

        $apiCacheService = new ApiCacheService;

        $response->withHeaders([
            'Surrogate-Key' => $apiCacheService->surrogateKeysForResource(
                resource: $this
            )->when(config('tenancy.enabled'), fn (Collection $headers) => $headers->push($apiCacheService->getTenantCacheTag()))->implode(' '),
        ]);
    }

    protected function isCacheableRequest(Request $request): bool
    {
        if ($request->getMethod() === 'GET') {
            return true;
        }

        return $request->getMethod() === 'HEAD';
    }
}
