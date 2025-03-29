<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Category;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasCategories
{
    public static ?string $categoriesAccessor = null;

    /**
     * @return BelongsToMany<Category, TModel>
     */
    public function categories(): BelongsToMany
    {
        /** @var TModel $this */
        $relationship = $this->belongsToMany(Category::class, "{$this->getTable()}_categories")
            ->where('resource', get_class($this))
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
