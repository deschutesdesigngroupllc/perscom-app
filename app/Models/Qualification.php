<?php

namespace App\Models;

use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Qualification extends Model implements Sortable
{
    use HasFactory;
    use HasImages;
    use SortableTrait;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'description'];

    /**
     * @var string[]
     */
    protected $with = ['image'];
}
