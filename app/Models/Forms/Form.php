<?php

namespace App\Models\Forms;

use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;
use Spatie\Tags\HasTags;

class Form extends Model
{
    use Actionable;
    use HasFactory;
    use HasTags;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['fields'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->slug ? route('form', [
            'slug' => $this->slug,
        ]) : null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function fields()
    {
        return $this->morphToMany(Field::class, 'model', 'model_has_fields')->withPivot(['order'])->withTimestamps();
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
