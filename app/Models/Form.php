<?php

namespace App\Models;

use App\Traits\HasFields;
use App\Traits\HasNotifications;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravel\Nova\Actions\Actionable;

/**
 * App\Models\Form
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Action> $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Field> $fields
 * @property-read int|null $fields_count
 * @property-read string $url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 *
 * @method static \Database\Factories\FormFactory factory($count = null, $state = [])
 * @method static Builder|Form forTags($tag)
 * @method static Builder|Form newModelQuery()
 * @method static Builder|Form newQuery()
 * @method static Builder|Form query()
 *
 * @mixin \Eloquent
 */
class Form extends Model
{
    use Actionable;
    use HasFactory;
    use HasFields;
    use HasNotifications;

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
     * @return Builder
     */
    public function scopeForTags(Builder $query, $tag)
    {
        $tags = Arr::wrap($tag);

        return $query->whereHas('tags', function (Builder $query) use ($tags) {
            $query->whereIn('name', $tags);
        });
    }

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
