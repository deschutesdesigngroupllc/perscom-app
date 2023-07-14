<?php

namespace App\Models;

use App\Models\Scopes\CombatRecordScope;
use App\Prompts\CombatRecordPrompts;
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
 * App\Models\CombatRecord
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Document|null $document
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\CombatRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord forAuthor(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord forDocument($document)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord query()
 *
 * @mixin \Eloquent
 */
class CombatRecord extends Model
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
    protected static $prompts = CombatRecordPrompts::class;

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
    protected $table = 'records_combat';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new CombatRecordScope);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('newsfeed')
            ->setDescriptionForEvent(fn ($event) => "A combat record has been $event");
    }

    /**
     * @return void
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName === 'created') {
            $activity->properties = $activity->properties->put('headline', "A combat record has been added for {$this->user->name}");
            $activity->properties = $activity->properties->put('text', $this->text);
        }
    }
}
