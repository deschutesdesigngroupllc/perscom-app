<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Element extends MorphPivot implements Sortable
{
    use SortableTrait;

    /**
     * @var string
     */
    protected $table = 'model_has_fields';
}
