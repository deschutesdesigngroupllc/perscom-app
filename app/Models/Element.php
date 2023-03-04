<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Element
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Element newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Element newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Element ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Element query()
 *
 * @mixin \Eloquent
 */
class Element extends MorphPivot implements Sortable
{
    use SortableTrait;

    /**
     * @var string
     */
    protected $table = 'model_has_fields';
}
