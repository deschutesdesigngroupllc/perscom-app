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
 * @method static \Database\Factories\QualificationRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationRecord forAuthor($user)
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationRecord forDocument($document)
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationRecord forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationRecord query()
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

    /**
     * @var string
     */
    protected static $prompts = QualificationRecordPrompts::class;

    /**
     * @var string[]
     */
    protected static $recordEvents = ['created'];

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'qualification_id', 'document_id', 'author_id', 'text'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'records_qualifications';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new QualificationRecordScope);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('newsfeed')
            ->setDescriptionForEvent(fn ($event) => "A qualification record has been $event");
    }

    /**
     * @return void
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName === 'created') {
            $activity->properties = $activity->properties->put('headline', "A qualification record has been added for {$this->user->name}");
            $activity->properties = $activity->properties->put('text', $this->text);
            $activity->properties = $activity->properties->put('subject', "Qualification: {$this->qualification->name}");
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }
}
