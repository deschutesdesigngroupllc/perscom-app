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
        $author = optional($this->resource?->causer?->name, function ($name) {
            return Str::title($name);
        });
        $authorProfilePhotoUrl = optional($this->resource?->causer)->profile_photo_url;
        $recipient = optional($this->resource?->subject?->user?->name, function ($name) {
            return Str::title($name);
        });
        $recipientProfilePhotoUrl = optional($this->resource?->subject?->user)->profile_photo_url;
        $model = optional($this->resource?->subject_type, function ($type) {
            return Str::replace('_', ' ', Str::snake(class_basename($type)));
        });

        Nova::resourcesIn(app_path('Nova'));

        $payload = [
            'id' => $this->resource->id,
            'author' => $author ?? null,
            'author_profile_photo' => $authorProfilePhotoUrl ?? null,
            'recipient' => $recipient ?? null,
            'recipient_profile_photo' => $recipientProfilePhotoUrl ?? null,
            'description' => $this->resource->description,
            'event' => $this->resource->event,
            'type' => $model ?? 'message',
            'headline' => $this->resource->getExtraProperty('headline'),
            'text' => $this->resource->getExtraProperty('text'),
            'subject' => $this->resource->getExtraProperty('subject'),
            'color' => $this->resource->getExtraProperty('color'),
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
