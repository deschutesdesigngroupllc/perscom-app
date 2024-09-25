<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\ApiCacheException;
use App\Services\ApiCacheService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PurgeApiCache implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected Model $model)
    {
        $this->onQueue('api');
    }

    /**
     * @throws ConnectionException
     */
    public function handle(): void
    {
        $apiService = new ApiCacheService;
        $response = $apiService->purgeCacheForModel($this->model);

        if (! $response->successful()) {
            $this->fail(new ApiCacheException(
                body: $response->json()
            ));
        }
    }
}
