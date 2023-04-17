<?php

namespace App\Traits;

trait HasResourceUrlAttribute
{
    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        return optional($this->getKey(), function ($key) {
            return route('nova.pages.detail', [
                'resource' => \call_user_func_array([$this->findNovaResourceClass(), 'uriKey'], []),
                'resourceId' => $key,
            ]);
        });
    }

    /**
     * @return string
     */
    public function getRelativeUrlAttribute()
    {
        return optional($this->getKey(), function ($key) {
            return route('nova.pages.detail', [
                'resource' => \call_user_func_array([$this->findNovaResourceClass(), 'uriKey'], []),
                'resourceId' => $key,
            ], false);
        });
    }

    /**
     * @return string
     */
    protected function findNovaResourceClass()
    {
        $class = class_basename(static::class);

        return "\\App\\Nova\\$class";
    }
}
