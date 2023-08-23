<?php

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait HasTags
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, $this->getTable().'_tags');
    }

    /**
     * @return Builder
     */
    public function scopeTags(Builder $query, $tag)
    {
        $tags = Arr::wrap($tag);

        return $query->whereHas('tags', function (Builder $query) use ($tags) {
            $query->whereIn('name', $tags);
        });
    }
}
