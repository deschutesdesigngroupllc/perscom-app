<?php

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Announcement
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 *
 * @method static \Database\Factories\AnnouncementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Announcement extends Model
{
    use ClearsResponseCache;
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    /**
     * @var string[]
     */
    protected static array $recordEvents = ['created'];

    /**
     * @var array<int, string>
     */
    protected $fillable = ['title', 'content', 'color', 'expires_at', 'updated_at', 'created_at'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('newsfeed')
            ->setDescriptionForEvent(fn ($event) => "An announcement has been $event");
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        if ($eventName === 'created') {
            $activity->properties = $activity->properties->put('headline', $this->title);
            $activity->properties = $activity->properties->put('text', $this->content);
            $activity->properties = $activity->properties->put('color', $this->color);
        }
    }
}
