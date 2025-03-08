<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs;

use App\Jobs\PurgeApiCache;
use App\Models\Rank;
use App\Services\ApiCacheService;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class PurgeApiCacheTest extends TenantTestCase
{
    public function test_creating_a_model_dispatches_the_job(): void
    {
        Queue::fake();

        $rank = Rank::factory()->create();

        Queue::assertPushed(PurgeApiCache::class, fn (PurgeApiCache $job): bool => in_array(ApiCacheService::tagForModel($rank), $job->tags->toArray())
            && in_array(ApiCacheService::tagForModel($rank, stripKey: true), $job->tags->toArray())
            && $job->event === 'created');
    }

    public function test_updating_a_model_dispatches_the_job(): void
    {
        Queue::fake();

        $rank = Rank::factory()->createQuietly();
        $rank->update([
            'name' => $this->faker->word,
        ]);

        Queue::assertPushed(PurgeApiCache::class, fn (PurgeApiCache $job): bool => $job->tags === ApiCacheService::tagForModel($rank)
            && $job->event === 'updated');
    }

    public function test_deleting_a_model_dispatches_the_job(): void
    {
        Queue::fake();

        $rank = Rank::factory()->createQuietly();
        $rank->delete();

        Queue::assertPushed(PurgeApiCache::class, fn (PurgeApiCache $job): bool => $job->tags === ApiCacheService::tagForModel($rank)
            && $job->event === 'deleted');
    }
}
