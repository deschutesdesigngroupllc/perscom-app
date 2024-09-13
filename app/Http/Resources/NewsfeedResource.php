<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orion\Http\Resources\Resource;

class NewsfeedResource extends Resource
{
    public function toArray(Request $request): array
    {
        $author = optional($this->resource?->causer?->name, function ($name) {
            return Str::title($name);
        });
        $authorProfilePhotoUrl = optional($this->resource?->causer)->profile_photo_url;
        $recipient = optional($this->resource?->subject?->name, function ($name) {
            return Str::title($name);
        });
        $recipientProfilePhotoUrl = optional($this->resource?->subject)->profile_photo_url;
        $model = optional($this->resource?->subject_type, function ($type) {
            return Str::replace('_', ' ', Str::snake(class_basename($type)));
        });

        return [
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
    }
}
