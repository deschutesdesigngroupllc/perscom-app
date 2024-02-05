<?php

namespace App\Traits;

use App\Models\Domain;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasResourceUrlAttribute
{
    public function url(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                return optional($this->getKey(), function ($key) {
                    return optional(tenant('domain'), function (Domain $domain) use ($key) {
                        return tenant_route($domain->host, 'nova.pages.detail', [
                            'resource' => \call_user_func_array([$this->findNovaResourceClass(), 'uriKey'], []),
                            'resourceId' => $key,
                        ]);
                    });
                });
            }
        );
    }

    public function relativeUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                return optional($this->getKey(), function ($key) {
                    return route('nova.pages.detail', [
                        'resource' => \call_user_func_array([$this->findNovaResourceClass(), 'uriKey'], []),
                        'resourceId' => $key,
                    ], false);
                });
            }
        );
    }

    protected function findNovaResourceClass(): string
    {
        $class = class_basename(static::class);

        return "\\App\\Nova\\$class";
    }
}
