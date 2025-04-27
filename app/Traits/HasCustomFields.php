<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Element;
use App\Models\Field;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin Eloquent
 */
trait HasCustomFields
{
    /**
     * @return MorphToMany<Field, $this>
     */
    public function fields(): MorphToMany
    {
        return $this->morphToMany(Field::class, 'model', 'model_has_fields')
            ->using(Element::class)
            ->as('fields')
            ->withPivot(['order'])
            ->orderBy('order')
            ->withTimestamps();
    }
}
