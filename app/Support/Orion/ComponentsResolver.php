<?php

declare(strict_types=1);

namespace App\Support\Orion;

use App\Http\Resources\Api\ApiResource;
use App\Http\Resources\Api\ApiResourceCollection;
use Illuminate\Support\Str;
use Orion\Drivers\Standard\ComponentsResolver as StandardComponentsResolver;
use Orion\Http\Resources\Resource;

class ComponentsResolver extends StandardComponentsResolver
{
    public function resolveResourceClass(): string
    {
        $resource = parent::resolveResourceClass();

        if ($resource === Resource::class) {
            return ApiResource::class;
        }

        return $resource;
    }

    public function resolveCollectionResourceClass(): ?string
    {
        $resource = parent::resolveCollectionResourceClass();

        if (! is_null($resource)) {
            return $resource;
        }

        return ApiResourceCollection::class;
    }

    public function getResourceClassesNamespace(): string
    {
        $requestedVersion = Str::upper(request()->route('version'));

        foreach (array_reverse(config('api.versions', [])) as $version) {
            $versionFormatted = Str::upper($version);

            $resourceClass = class_basename($this->resourceModelClass).'Resource';
            $resourceClassNamespace = "App\\Http\\Resources\\Api\\$versionFormatted\\";

            if (class_exists($resourceClassNamespace.$resourceClass) && $requestedVersion === $versionFormatted) {
                return $resourceClassNamespace;
            }
        }

        return parent::getResourceClassesNamespace();
    }
}
