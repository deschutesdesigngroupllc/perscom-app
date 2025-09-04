<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasComments;
use App\Traits\HasDocument;
use App\Traits\HasEvent;
use App\Traits\HasLogs;
use App\Traits\HasModelNotifications;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $instructor_id
 * @property int|null $author_id
 * @property int|null $document_id
 * @property int|null $event_id
 * @property string|null $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @property-read Collection<int, Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read User|null $author
 * @property-read Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 * @property-read TrainingRecordCredential|TrainingRecordCompetency|null $pivot
 * @property-read Collection<int, Competency> $competencies
 * @property-read int|null $competencies_count
 * @property-read Collection<int, Credential> $credentials
 * @property-read int|null $credentials_count
 * @property-read Document|null $document
 * @property-read mixed $document_parsed
 * @property-read Event|null $event
 * @property-read User|null $instructor
 * @property-read Collection<int, Activity> $logs
 * @property-read int|null $logs_count
 * @property-read Collection<int, ModelNotification> $modelNotifications
 * @property-read int|null $model_notifications_count
 * @property-read User|null $user
 *
 * @method static Builder<static>|TrainingRecord author(\App\Models\User $user)
 * @method static Builder<static>|TrainingRecord document(\App\Models\Document $document)
 * @method static Builder<static>|TrainingRecord event(\App\Models\Event $event)
 * @method static \Database\Factories\TrainingRecordFactory factory($count = null, $state = [])
 * @method static Builder<static>|TrainingRecord newModelQuery()
 * @method static Builder<static>|TrainingRecord newQuery()
 * @method static Builder<static>|TrainingRecord query()
 * @method static Builder<static>|TrainingRecord user(\App\Models\User $user)
 * @method static Builder<static>|TrainingRecord whereAuthorId($value)
 * @method static Builder<static>|TrainingRecord whereCreatedAt($value)
 * @method static Builder<static>|TrainingRecord whereDocumentId($value)
 * @method static Builder<static>|TrainingRecord whereEventId($value)
 * @method static Builder<static>|TrainingRecord whereId($value)
 * @method static Builder<static>|TrainingRecord whereInstructorId($value)
 * @method static Builder<static>|TrainingRecord whereText($value)
 * @method static Builder<static>|TrainingRecord whereUpdatedAt($value)
 * @method static Builder<static>|TrainingRecord whereUserId($value)
 *
 * @mixin \Eloquent
 */
class TrainingRecord extends Model
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasAttachments;
    use HasAuthor;
    use HasComments;
    use HasDocument;
    use HasEvent;
    use HasFactory;
    use HasLogs;
    use HasModelNotifications;
    use HasUser;

    protected $table = 'records_trainings';

    protected $fillable = [
        'instructor_id',
        'text',
    ];

    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(Competency::class, 'records_trainings_competencies')
            ->using(TrainingRecordCompetency::class);
    }

    public function credentials(): BelongsToMany
    {
        return $this->belongsToMany(Credential::class, 'records_trainings_credentials')
            ->using(TrainingRecordCredential::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
