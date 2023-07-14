<?php

namespace App\Models;

use App\Models\Scopes\ServiceRecordScope;
use App\Prompts\ServiceRecordPrompts;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasDocument;
use App\Traits\HasEventPrompts;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\ServiceRecord
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Document|null $document
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\ServiceRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord forAuthor(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord forDocument($document)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord query()
 *
 * @mixin \Eloquent
 */
class ServiceRecord extends Model
{
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasEventPrompts;
    use HasFactory;
    use HasUser;
    use LogsActivity;

    /**
     * @var string
     */
    public static $prompts = ServiceRecordPrompts::class;

    /**
     * @var string[]
     */
    protected static $recordEvents = ['created'];

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'document_id', 'author_id', 'text'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'records_service';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ServiceRecordScope);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('newsfeed')
            ->setDescriptionForEvent(fn ($event) => "A service record has been $event");
    }

    /**
     * @return void
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName === 'created') {
            $activity->properties = $activity->properties->put('headline', "A service record has been added for {$this->user->name}");
            $activity->properties = $activity->properties->put('text', $this->text);
        }
    }
}
