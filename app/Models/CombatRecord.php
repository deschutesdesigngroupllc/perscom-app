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
 * @property int $id
 * @property int|null $user_id
 * @property int|null $document_id
 * @property int|null $author_id
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Document|null $document
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord author(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord document(\App\Models\Document $document)
 * @method static \Database\Factories\CombatRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord user(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereUserId($value)
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

    protected static string $prompts = CombatRecordPrompts::class;

    /**
     * @var string[]
     */
    protected static array $recordEvents = ['created'];

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'document_id', 'author_id', 'text', 'updated_at', 'created_at'];

    /**
     * @var string
     */
    protected $table = 'records_combat';

    protected static function booted(): void
    {
        static::addGlobalScope(new CombatRecordScope);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('newsfeed')
            ->setDescriptionForEvent(fn ($event) => "A combat record has been $event");
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        if ($eventName === 'created') {
            $activity->properties = $activity->properties->put('headline', "A combat record has been added for {$this->user->name}");
            $activity->properties = $activity->properties->put('text', $this->text);
        }
    }
}
