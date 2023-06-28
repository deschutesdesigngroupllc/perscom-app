<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Announcement
 *
 * @method static \Database\Factories\AnnouncementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement query()
 *
 * @mixin \Eloquent
 */
class Announcement extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * @var string[]
     */
    protected static $recordEvents = ['created'];

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'content', 'color', 'expires_at'];

    /**
     * The attributes that should be cast.
     *
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

    /**
     * @return void
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName === 'created') {
            $activity->properties = $activity->properties->put('headline', $this->title);
            $activity->properties = $activity->properties->put('text', $this->content);
        }
    }
}
