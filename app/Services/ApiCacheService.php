<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\Api\ApiResource;
use App\Http\Resources\Api\ApiResourceCollection;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ApiCacheService
{
    public function __construct(protected ?Tenant $tenant = null)
    {
        $this->tenant ??= tenant();
    }

    public function getCacheTag(Model $model): string
    {
        return $this->getCachePrefix($model).':'.$model->getKey();
    }

    public function surrogateKeysForResource(ApiResource|ApiResourceCollection $resource): Collection
    {
        $tags = collect();

        $resources = $resource instanceof ApiResource
            ? Arr::wrap($resource)
            : $resource->resource->items();

        foreach ($resources as $resource) {
            if ($resource instanceof ApiResource) {
                $tags->push($this->getCachePrefix($resource->resource));
                $tags->push($this->getCacheTag($resource->resource));
                $this->getRelationKeys($resource->resource, $tags);
            }
        }

        return $tags->unique();
    }

    /**
     * @throws ConnectionException
     */
    public function purgeCacheForModel(Model $model): Response
    {
        $tags = collect();
        $tags->push($this->getCachePrefix($model));
        $tags->push($this->getCacheTag($model));

        $service = config('services.fastly.service');

        return Http::baseUrl(config('services.fastly.base_url'))
            ->withHeader('Fastly-Key', config('services.fastly.token'))
            ->withHeader('Fastly-Soft-Purge', 1)
            ->post("/service/$service/purge", [
                'surrogate_keys' => $tags->unique()->toArray(),
            ]);
    }

    protected function getRelationKeys(Model $model, Collection $tags): void
    {
        foreach ($model->getRelations() as $relation) {
            if ($relation instanceof Collection) {
                foreach ($relation as $relatedModel) {
                    $tags->push($this->getCachePrefix($relatedModel));
                    $tags->push($this->getCacheTag($relatedModel));
                    $this->getRelationKeys($relatedModel, $tags);
                }
            } elseif ($relation instanceof Model) {
                $tags->push($this->getCachePrefix($relation));
                $tags->push($this->getCacheTag($relation));
                $this->getRelationKeys($relation, $tags);
            }
        }
    }

    protected function getCachePrefix(Model $model): string
    {
        return strtolower($this->tenant->getTenantKey().':'.class_basename($model));
    }
}
