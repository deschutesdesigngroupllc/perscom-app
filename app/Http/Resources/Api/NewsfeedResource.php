<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orion\Http\Resources\Resource;

class NewsfeedResource extends Resource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User|null $author */
        $author = $this->resource?->causer;

        /** @var User|null $recipient */
        $recipient = $this->resource?->subject;

        return [
            'id' => $this->resource->id,
            'author' => optional($author, fn (User $user) => Str::title($user->name)),
            'author_profile_photo' => optional($author, fn (User $user) => $user->profile_photo_url),
            'recipient' => optional($recipient, fn (User $user) => Str::title($user->name)),
            'recipient_profile_photo' => optional($recipient, fn (User $user) => $user->profile_photo_url),
            'description' => $this->resource->description,
            'event' => $this->resource->event,
            'type' => optional($this->resource->subject_type, fn ($subjectType) => Str::replace('_', ' ', Str::snake(class_basename($subjectType)))) ?? 'message',
            'headline' => $this->resource->headline,
            'text' => $this->resource->text,
            'item' => $this->resource->item,
            'color' => $this->resource->color,
            'likes' => $this->resource->likes,
            'created_at' => $this->resource->created_at,
        ];
    }
}
