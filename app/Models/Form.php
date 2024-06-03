<?php

namespace App\Models;

use App\Traits\ClearsResponseCache;
use App\Traits\HasFields;
use App\Traits\HasNotifications;
use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Nova\Actions\Actionable;

/**
 * App\Models\Form
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $success_message
 * @property int|null $submission_status_id
 * @property int $is_public
 * @property string|null $description
 * @property string|null $instructions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Action> $actions
 * @property-read int|null $actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Field> $fields
 * @property-read int|null $fields_count
 * @property-read string $url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Status|null $submission_status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 *
 * @method static \Database\Factories\FormFactory factory($count = null, $state = [])
 * @method static Builder|Form newModelQuery()
 * @method static Builder|Form newQuery()
 * @method static Builder|Form onlyTrashed()
 * @method static Builder|Form query()
 * @method static Builder|Form tags(?mixed $tag)
 * @method static Builder|Form whereCreatedAt($value)
 * @method static Builder|Form whereDeletedAt($value)
 * @method static Builder|Form whereDescription($value)
 * @method static Builder|Form whereId($value)
 * @method static Builder|Form whereInstructions($value)
 * @method static Builder|Form whereIsPublic($value)
 * @method static Builder|Form whereName($value)
 * @method static Builder|Form whereSlug($value)
 * @method static Builder|Form whereSubmissionStatusId($value)
 * @method static Builder|Form whereSuccessMessage($value)
 * @method static Builder|Form whereUpdatedAt($value)
 * @method static Builder|Form withTrashed()
 * @method static Builder|Form withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Form extends Model
{
    use Actionable;
    use ClearsResponseCache;
    use HasFactory;
    use HasFields;
    use HasNotifications;
    use HasTags;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $appends = ['url'];

    /**
     * @var string[]
     */
    protected $with = ['fields'];

    /**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'slug', 'success_message', 'is_public', 'description', 'instructions', 'updated_at', 'created_at'];

    public function getUrlAttribute(): string
    {
        return route('nova.pages.create', [
            'resource' => \App\Nova\Submission::uriKey(),
            'viaResource' => \App\Nova\Form::uriKey(),
            'viaResourceId' => $this->id,
        ]);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'forms_categories')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function submission_status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
