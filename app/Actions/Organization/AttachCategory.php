<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Award;
use App\Models\Category;
use App\Models\Qualification;
use App\Models\Rank;
use Illuminate\Support\Str;

class AttachCategory
{
    /**
     * Find or create a category by name for the model's resource type and attach it to the model.
     */
    public static function handle(Award|Qualification|Rank $model, string $name): Category
    {
        $category = Category::query()
            ->where('resource', $model::class)
            ->whereRaw('LOWER(name) = ?', [Str::lower($name)])
            ->first() ?? Category::create([
                'name' => $name,
                'resource' => $model::class,
            ]);

        if (! $model->categories()->whereKey($category->getKey())->exists()) {
            $model->categories()->attach($category->getKey());
        }

        return $category;
    }
}
