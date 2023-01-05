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

    /**
     * @var bool
     */
    public $incrementing = true;

    /**
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildSortQuery()
    {
        return static::query()->where('model_id', $this->model_id);
    }
}
