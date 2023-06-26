<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Nova;
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

        Nova::resourcesIn(app_path('Nova'));

        return parent::toArrayWithMerge($request, [
            'newsfeed' => [
                'author' => $causer,
                'event' => $this->resource->description,
                'type' => $model,
                'url' => tenant()->route('nova.pages.detail', [
                    'resource' => Nova::resourceForModel($this->resource->subject)::uriKey(),
                    'resourceId' => $this->resource->subject->id,
                ]),
            ],
        ]);
    }
}
