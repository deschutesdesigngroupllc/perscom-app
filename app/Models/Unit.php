<?php

namespace App\Models;

use App\Models\Records\Assignment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Unit extends Model implements Sortable
{
    use HasFactory;
    use SortableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description', 'order'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignment_records()
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
