<?php

namespace App\Models;

use App\Traits\HasFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 *
 * @method static \Database\Factories\FormFactory factory($count = null, $state = [])
 * @method static Builder|Form forTags(string $tag)
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

    /**
     * @var string[]
     */
    protected $appends = ['url'];

    /**
     * @var string[]
     */
    protected $with = ['fields'];

    public function scopeForTags(Builder $query, string $tag): void
    {
        $tags = Arr::wrap($tag);

        $query->whereHas('tags', function (Builder $query) use ($tags) {
            $query->whereIn('name', $tags);
        });
    }

    public function getUrlAttribute(): string
    {
        return route('nova.pages.create', [
            'resource' => \App\Nova\Submission::uriKey(),
            'viaResource' => \App\Nova\Form::uriKey(),
            'viaResourceId' => $this->id,
        ]);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'forms_tags');
    }
}
