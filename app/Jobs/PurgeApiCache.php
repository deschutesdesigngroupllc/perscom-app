<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\ApiCacheException;
use App\Services\ApiCacheService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class PurgeApiCache implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    protected string $overlapHash;

    public function __construct(public Collection|string $tags, public string $event)
    {
        $this->overlapHash = md5(Collection::wrap($this->tags)->implode(','));
        $this->onQueue('api');
    }

    public function middleware(): array
    {
        return [
            new WithoutOverlapping($this->overlapHash),
            Skip::when(fn (): bool => App::environment('local')),
        ];
    }

    /**
     * @throws ConnectionException
     */
    public function handle(): void
    {
        $apiService = new ApiCacheService;

        $responses = $apiService->purgeCacheForTags($this->tags);

        foreach ($responses as $response) {
            if (! $response->successful()) {
                $this->fail(new ApiCacheException(
                    body: $response->json()
                ));
            }
        }
    }
}
