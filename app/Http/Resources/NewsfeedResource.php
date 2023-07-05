<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
            'headline' => $this->resource->headline,
            'text' => $this->resource->text,
            'item' => $this->resource->item,
            'color' => $this->resource->color,
            'likes' => $this->resource->likes,
            'created_at' => $this->resource->created_at,
        ];

        if ($this->resource->subject && Gate::check('view', $this->resource->subject)) {
            $payload['url'] = tenant()->route('nova.pages.detail', [
                'resource' => Nova::resourceForModel($this->resource->subject)::uriKey(),
                'resourceId' => $this->resource->subject->id,
            ]);
        }

        return $payload;
    }
}
