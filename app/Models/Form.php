<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\HasFields;
use App\Filament\App\Resources\FormResource;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasCategories;
use App\Traits\HasCustomFields;
use App\Traits\HasNotifications;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasTags;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $success_message
 * @property int|null $submission_status_id
 * @property bool $is_public
 * @property string|null $description
 * @property string|null $instructions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Field> $fields
 * @property-read int|null $fields_count
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Support\Optional|string|null|null $relative_url
 * @property-read Status|null $submission_status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read string $url
 *
 * @method static \Database\Factories\FormFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Form newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Form newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Form onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Form query()
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereSubmissionStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereSuccessMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Form withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Form withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Form extends Model implements HasFields, HasLabel
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasCategories;
    use HasCustomFields;
    use HasFactory;
    use HasNotifications;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasTags;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'success_message',
        'is_public',
        'description',
        'instructions',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = ['url'];

    /**
     * @return Attribute<string, void>
     */
    public function url(): Attribute
    {
        return Attribute::make(
            get: fn (): string => FormResource::getUrl('create', panel: 'app')
        );
    }

    /**
     * @return HasMany<Submission>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * @return BelongsTo<Status, Form>
     */
    public function submission_status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }
}
