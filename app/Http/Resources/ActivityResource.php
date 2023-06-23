<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orion\Http\Resources\Resource;

class ActivityResource extends Resource
{
    /**
     * @return array
     */
    public function toArray(Request $request)
    {
        $causer = Str::title($this->resource->causer->name);
        $model = Str::replace('_', ' ', Str::snake(class_basename($this->resource->subject_type)));

        return parent::toArrayWithMerge($request, [
            'newsfeed' => [
                'author' => $causer,
                'title' => "{$this->resource->description} a $model",
            ],
        ]);
    }
}
