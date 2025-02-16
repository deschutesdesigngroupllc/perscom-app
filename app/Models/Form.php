<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\SendsModelNotifications;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasCategories;
use App\Traits\HasCustomFields;
use App\Traits\HasModelNotifications;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasTags;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Field> $fields
 * @property-read int|null $fields_count
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ModelNotification> $modelNotifications
 * @property-read int|null $model_notifications_count
 * @property-read string|null $relative_url
 * @property-read Status|null $submission_status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Submission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 * @property-read int|null $tags_count
 * @property-read string|null $url
 *
 * @method static \Database\Factories\FormFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form whereSubmissionStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form whereSuccessMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Form whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Form extends Model implements HasLabel, SendsModelNotifications
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasCategories;
    use HasCustomFields;
    use HasFactory;
    use HasModelNotifications;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasTags;

    protected $fillable = [
        'name',
        'slug',
        'success_message',
        'is_public',
        'description',
        'instructions',
        'created_at',
        'updated_at',
    ];

    /**
     * @return HasMany<Submission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * @return BelongsTo<Status, $this>
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
