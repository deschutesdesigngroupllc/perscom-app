<?php

declare(strict_types=1);

namespace App\Support\Orion;

use Illuminate\Support\Str;
use Orion\Drivers\Standard\ComponentsResolver as StandardComponentsResolver;

class ComponentsResolver extends StandardComponentsResolver
{
    public function resolveResourceClass(): string
    {
        $resource = parent::resolveResourceClass();

        $requestedVersion = Str::of(request()->segment(1))->upper()->squish()->replace('.', '')->toString();

        foreach (array_reverse(config('api.versions', [])) as $version) {
            $versionFormatted = Str::of($version)->upper()->squish()->replace('.', '')->toString();

            $resourceClassName = $this->getResourceClassesNamespace().'Api\\'.$versionFormatted.'\\'.class_basename($this->resourceModelClass).'Resource';
            if (class_exists($resourceClassName) && $requestedVersion === $versionFormatted) {
                $resource = $resourceClassName;

                break;
            }
        }

        return $resource;
    }
}
