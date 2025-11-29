<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Http\Resources\Api\ApiResourceCollection;
use App\Models\Rank;
use App\Services\ApiCacheService;
use Tests\Feature\Tenant\TenantTestCase;

class ApiCacheServiceTest extends TenantTestCase
{
    public function test_tag_for_model_is_correctly_generated(): void
    {
        $rank = Rank::factory()->createQuietly();

        $this->assertTrue(ApiCacheService::tagForModel($rank) === sprintf('%s:rank:%s', $this->tenant->getKey(), $rank->getKey()));
    }

    public function test_cache_tag_is_correctly_generated(): void
    {
        $rank = Rank::factory()->createQuietly();
        $service = new ApiCacheService;

        $this->assertTrue($service->getCacheTag($rank) === sprintf('%s:rank:%s', $this->tenant->getKey(), $rank->getKey()));
    }

    public function test_surrogate_keys_are_correctly_generated(): void
    {
        $rank = Rank::factory()->count(5)->createQuietly();
        $collection = ApiResourceCollection::make($rank);
        $service = new ApiCacheService;

        $keys = $service->surrogateKeysForResource($collection)->toArray();

        $this->assertTrue(in_array($this->tenant->getKey().':rank', $keys));

        $rank->each(function (Rank $rank) use ($keys): void {
            $this->assertTrue(in_array(sprintf('%s:rank:%s', $this->tenant->getKey(), $rank->getKey()), $keys));
        });
    }
}
