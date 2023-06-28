<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Nova;
use Orion\Http\Resources\Resource;

class NewsfeedResource extends Resource
{
    /**
     * @return array
     */
    public function toArray(Request $request)
    {
        $causer = $this->resource->causer ? Str::title($this->resource->causer->name) : null;
        $model = $this->resource->subject ? Str::replace('_', ' ', Str::snake(class_basename($this->resource->subject_type))) : null;

        Nova::resourcesIn(app_path('Nova'));

        $payload = [
            'id' => $this->resource->id,
            'author' => $causer ?? null,
            'subject' => $this->resource->subject,
            'causer' => $this->resource->causer,
            'description' => $this->resource->description,
            'event' => $this->resource->event,
            'type' => $model ?? 'message',
            'headline' => $this->resource->getExtraProperty('headline'),
            'text' => $this->resource->getExtraProperty('text'),
            'created_at' => $this->resource->created_at,
        ];

        if ($this->resource->subject) {
            $payload['url'] = tenant()->route('nova.pages.detail', [
                'resource' => Nova::resourceForModel($this->resource->subject)::uriKey(),
                'resourceId' => $this->resource->subject->id,
            ]);
        }

        return $payload;
    }
}
