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
    public static ?string $categoriesAccessor = null;

    public function categories(): BelongsToMany
    {
        $relationship = $this->belongsToMany(Category::class, $this->getTable().'_categories')
            ->where('resource', $this::class)
            ->withPivot('order')
            ->withTimestamps();

        if (filled(static::$categoriesAccessor)) {
            return $relationship->as(static::$categoriesAccessor);
        }

        return $relationship;
    }

    protected function initializeHasCategories(): void
    {
        $class = Str::singular(class_basename($this)).'Category';

        if (class_exists($class)) {
            static::$categoriesAccessor = $class;
        }
    }
}
