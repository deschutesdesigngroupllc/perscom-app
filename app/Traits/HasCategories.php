<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Category;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @mixin Eloquent
 */
trait HasCategories
{
    private ?string $categoriesAccessor = null;

    public function categories(): BelongsToMany
    {
        $relationship = $this->belongsToMany(Category::class, "{$this->getTable()}_categories")
            ->withPivot('order')
            ->withTimestamps();

        if ($this->categoriesAccessor) {
            $relationship = $relationship->as($this->categoriesAccessor);
        }

        return $relationship;
    }

    protected function initializeHasCategories(): void
    {
        $class = Str::singular(class_basename($this)).'Category';

        if (class_exists($class)) {
            $this->categoriesAccessor = $class;
        }
    }
}
