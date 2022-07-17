<?php

namespace App\Models\Forms;

use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];

    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        return route('form', [
            'slug' => $this->slug,
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function fields()
    {
        return $this->morphToMany(Field::class, 'model', 'model_has_fields')
            ->withPivot(['order'])
            ->withTimestamps();
    }
}
