<?php

namespace App\Traits;

trait HasResourceUrlAttribute
{
    public function getUrlAttribute(): ?string
    {
        return optional($this->getKey(), function ($key) {
            return route('nova.pages.detail', [
                'resource' => \call_user_func_array([$this->findNovaResourceClass(), 'uriKey'], []),
                'resourceId' => $key,
            ]);
        });
    }

    public function getRelativeUrlAttribute(): ?string
    {
        return optional($this->getKey(), function ($key) {
            return route('nova.pages.detail', [
                'resource' => \call_user_func_array([$this->findNovaResourceClass(), 'uriKey'], []),
                'resourceId' => $key,
            ], false);
        });
    }

    protected function findNovaResourceClass(): string
    {
        $class = class_basename(static::class);

        return "\\App\\Nova\\$class";
    }
}
