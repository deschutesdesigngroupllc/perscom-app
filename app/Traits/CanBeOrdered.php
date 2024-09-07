<?php

declare(strict_types=1);

namespace App\Traits;

use Eloquent;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @mixin Eloquent|Sortable
 */
trait CanBeOrdered
{
    use SortableTrait;

    public function determineOrderColumnName(): string
    {
        $table = $this->getTable();

        return "$table.order";
    }
}
