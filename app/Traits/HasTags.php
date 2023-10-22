<?php

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

trait HasTags
{
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, $this->getTable().'_tags');
    }

    public function scopeTags(Builder $query, mixed $tag): void
    {
        $tags = Arr::wrap($tag);

        $query->whereHas('tags', function (Builder $query) use ($tags) {
            $query->whereIn('name', $tags);
        });
    }
}
