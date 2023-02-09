<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;

/**
 * App\Models\Form
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Action> $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Field> $fields
 * @property-read int|null $fields_count
 * @property-read string $url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 *
 * @method static \Database\Factories\FormFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Form newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Form newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Form query()
 * @mixin \Eloquent
 */
class Form extends Model
{
    use Actionable;
    use HasFactory;

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
        return route('nova.pages.create', [
            'resource' => \App\Nova\Submission::uriKey(),
            'viaResource' => \App\Nova\Form::uriKey(),
            'viaResourceId' => $this->id,
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function fields()
    {
        return $this->morphToMany(Field::class, 'model', 'model_has_fields')
                    ->using(Element::class)
                    ->as('fields')
                    ->withPivot(['order'])
                    ->orderBy('order')
                    ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'forms_tags');
    }
}
