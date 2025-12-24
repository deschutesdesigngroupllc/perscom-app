<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\ApiPurgedFailed;
use App\Events\ApiPurgedSuccessful;
use App\Http\Resources\Api\ApiResource;
use App\Http\Resources\Api\ApiResourceCollection;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Pool;
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

    public static function tagForModel(Model $model, bool $stripKey = false): string
    {
        if ($stripKey) {
            return (new self)->getCachePrefix($model);
        }

        return (new self)->getCacheTag($model);
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
            : ($resource->resource instanceof Collection
                ? $resource->resource
                : $resource->resource->items()
            );

        foreach ($resources as $resource) {
            if ($resource instanceof ApiResource) {
                $tags->push($this->getCachePrefix($resource->resource));
                $tags->push($this->getCacheTag($resource->resource));
                $this->getRelationKeys($resource->resource, $tags);
            }
        }

        return $tags
            ->filter()
            ->unique()
            ->values();
    }

    public function purgeCacheForTags(Collection|string $tags, string $event): array
    {
        $tags = collect($tags);

        $baseUrl = config('services.fastly.base_url');
        $service = config('services.fastly.service');
        $url = sprintf('%s/service/%s/purge', $baseUrl, $service);

        $headers = [
            'Fastly-Key' => config('services.fastly.token'),
            'Fastly-Soft-Purge' => 1,
        ];

        $responses = Http::pool(fn (Pool $pool) => $tags->map(fn (string $tag) => $pool
            ->baseUrl($url)
            ->withHeaders($headers)
            ->post($tag)
        )->toArray());

        foreach ($responses as $response) {
            if ($response instanceof Response && ! $response->successful()) {
                event(new ApiPurgedFailed(
                    tags: $tags->toArray(),
                    event: $event,
                ));
            } else {
                event(new ApiPurgedSuccessful(
                    tags: $tags->toArray(),
                    event: $event,
                ));
            }
        }

        return $responses;
    }

    public function getTenantCacheTag(): string
    {
        return 'tenant:'.$this->tenant->getKey();
    }

    protected function getRelationKeys(Model $model, Collection $tags): void
    {
        foreach ($model->getRelations() as $relationName => $relation) {
            $tags->push($this->resolveRelationName($model, $relationName));

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
        if (! config('tenancy.enabled') || blank($this->tenant)) {
            return class_basename($model);
        }

        return strtolower($this->tenant->getKey().':'.class_basename($model));
    }

    protected function resolveRelationName(Model $model, string $relationName): ?string
    {
        if (! method_exists($model, $relationName)) {
            return null;
        }

        $relation = $model->{$relationName}();

        return $this->getCachePrefix($relation->getRelated());
    }
}
