<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Message extends Model implements Sortable
{
    use CentralConnection;
    use SortableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['title', 'message', 'active', 'order', 'url', 'link_text'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('active', '=', true);
    }
}
