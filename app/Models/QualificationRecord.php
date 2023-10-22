<?php

namespace App\Models;

use App\Models\Scopes\QualificationRecordScope;
use App\Prompts\QualificationRecordPrompts;
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
 * App\Models\QualificationRecord
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Document|null $document
 * @property-read \App\Models\Qualification|null $qualification
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationRecord author(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationRecord document(\App\Models\Document $document)
 * @method static \Database\Factories\QualificationRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationRecord user(\App\Models\User $user)
 *
 * @mixin \Eloquent
 */
class QualificationRecord extends Model
{
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasEventPrompts;
    use HasFactory;
    use HasUser;
    use LogsActivity;

    protected static string $prompts = QualificationRecordPrompts::class;

    /**
     * @var string[]
     */
    protected static array $recordEvents = ['created'];

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'qualification_id', 'document_id', 'author_id', 'text'];

    /**
     * @var string[]
     */
    protected $with = ['qualification'];

    /**
     * @var string
     */
    protected $table = 'records_qualifications';

    protected static function booted(): void
    {
        static::addGlobalScope(new QualificationRecordScope);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('newsfeed')
            ->setDescriptionForEvent(fn ($event) => "A qualification record has been $event");
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        if ($eventName === 'created') {
            $activity->properties = $activity->properties->put('headline', "A qualification record has been added for {$this->user->name}");
            $activity->properties = $activity->properties->put('text', $this->text);
            $activity->properties = $activity->properties->put('item', "Qualification: {$this->qualification->name}");
        }
    }

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(Qualification::class);
    }
}
