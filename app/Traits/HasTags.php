<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Tag;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @mixin Eloquent
 */
trait HasTags
{
    private ?string $tagsAccessor = null;

    public function tags(): BelongsToMany
    {
        $relationship = $this->belongsToMany(Tag::class, "{$this->getTable()}_tags")
            ->withPivot('order')
            ->withTimestamps();

        if ($this->tagsAccessor) {
            $relationship = $relationship->as($this->tagsAccessor);
        }

        return $relationship;
    }

    protected function initializeHasTags(): void
    {
        $class = Str::singular(class_basename($this)).'Tag';

        if (class_exists($class)) {
            $this->tagsAccessor = $class;
        }
    }
}
