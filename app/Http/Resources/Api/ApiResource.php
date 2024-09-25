<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Services\ApiCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Orion\Http\Resources\Resource;

class ApiResource extends Resource
{
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $apiCacheService = new ApiCacheService;

        $response->withHeaders([
            'Surrogate-Key' => $apiCacheService->surrogateKeysForResource(
                resource: $this->resource
            )->push('tenant:'.tenant()->getTenantKey())->implode(' '),
        ]);
    }
}
