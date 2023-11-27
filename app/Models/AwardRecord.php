<?php

namespace App\Models;

use App\Models\Scopes\AwardRecordScope;
use App\Prompts\AwardRecordPrompts;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasDocument;
use App\Traits\HasEventPrompts;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\AwardRecord
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $award_id
 * @property int|null $document_id
 * @property int|null $author_id
 * @property string|null $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Award|null $award
 * @property-read \App\Models\Document|null $document
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord author(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord document(\App\Models\Document $document)
 * @method static \Database\Factories\AwardRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord user(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord whereAwardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord whereUserId($value)
 *
 * @mixin \Eloquent
 */
class AwardRecord extends Model
{
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasEventPrompts;
    use HasFactory;
    use HasUser;
    use LogsActivity;

    protected static string $prompts = AwardRecordPrompts::class;

    /**
     * @var string[]
     */
    protected static array $recordEvents = ['created'];

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'award_id', 'document_id', 'author_id', 'text', 'updated_at', 'created_at'];

    /**
     * @var string[]
     */
    protected $with = ['award'];

    /**
     * @var string
     */
    protected $table = 'records_awards';

    protected static function booted(): void
    {
        static::addGlobalScope(new AwardRecordScope());
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('newsfeed')
            ->setDescriptionForEvent(fn ($event) => "An award record has been $event");
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        if ($eventName === 'created') {
            $activity->properties = $activity->properties->put('headline', "An award record has been added for {$this->user->name}");
            $activity->properties = $activity->properties->put('text', $this->text);
            $activity->properties = $activity->properties->put('item', "Award: {$this->award->name}");
        }
    }

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }
}
