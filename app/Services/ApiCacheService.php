<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
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
        return strtolower($this->tenant->getTenantKey().':'.class_basename($model)).':'.$model->getKey();
    }

    public function surrogateKeysForResource(Model $resource): Collection
    {
        $tags = collect();
        $tags->push($this->getCacheTag($resource));

        $this->getRelationKeys($resource, $tags);

        return $tags->unique();
    }

    /**
     * @throws ConnectionException
     */
    public function purgeCacheForModel(Model $model): Response
    {
        $tag = $this->getCacheTag($model);
        $service = config('services.fastly.service');

        return Http::baseUrl(config('services.fastly.base_url'))
            ->withHeader('Fastly-Key', config('services.fastly.token'))
            ->withHeader('Fastly-Soft-Purge', 1)
            ->post("/service/$service/purge/$tag");
    }

    protected function getRelationKeys(Model $model, Collection $keys): void
    {
        foreach ($model->getRelations() as $relation) {
            if ($relation instanceof Collection) {
                foreach ($relation as $relatedModel) {
                    $keys->push($this->getCacheTag($relatedModel));
                    $this->getRelationKeys($relatedModel, $keys);
                }
            } elseif ($relation instanceof Model) {
                $keys->push($this->getCacheTag($relation));
                $this->getRelationKeys($relation, $keys);
            }
        }
    }
}
