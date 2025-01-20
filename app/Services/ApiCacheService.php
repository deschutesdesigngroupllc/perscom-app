<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\Api\ApiResource;
use App\Http\Resources\Api\ApiResourceCollection;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Pool;
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

        return $tags->unique();
    }

    public function purgeCacheForTags(Collection|string $tags): array
    {
        $tags = collect($tags);

        $baseUrl = config('services.fastly.base_url');
        $service = config('services.fastly.service');
        $url = "$baseUrl/service/$service/purge";

        $headers = [
            'Fastly-Key' => config('services.fastly.token'),
            'Fastly-Soft-Purge' => 1,
        ];

        return Http::pool(function (Pool $pool) use ($headers, $tags, $url) {
            return $tags->map(fn ($tag) => $pool
                ->baseUrl($url)
                ->withHeaders($headers)
                ->post($tag)
            )->toArray();
        });
    }

    public function getTenantCacheTag(): string
    {
        return "tenant:{$this->tenant->getKey()}";
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
        return strtolower($this->tenant->getKey().':'.class_basename($model));
    }
}
