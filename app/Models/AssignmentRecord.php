<?php

namespace App\Models;

use App\Models\Enums\AssignmentRecordType;
use App\Models\Scopes\AssignmentRecordScope;
use App\Prompts\AssignmentRecordPrompts;
use App\Traits\ClearsResponseCache;
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
 * App\Models\AssignmentRecord
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $status_id
 * @property int|null $unit_id
 * @property int|null $position_id
 * @property int|null $specialty_id
 * @property int|null $document_id
 * @property int|null $author_id
 * @property AssignmentRecordType|null $type
 * @property string|null $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Document|null $document
 * @property-read \App\Models\Position|null $position
 * @property-read \App\Models\Specialty|null $specialty
 * @property-read \App\Models\Status|null $status
 * @property-read \App\Models\Unit|null $unit
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord author(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord document(\App\Models\Document $document)
 * @method static \Database\Factories\AssignmentRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord user(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord whereSpecialtyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord whereUserId($value)
 *
 * @mixin \Eloquent
 */
class AssignmentRecord extends Model
{
    use ClearsResponseCache;
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasEventPrompts;
    use HasFactory;
    use HasUser;
    use LogsActivity;

    protected static string $prompts = AssignmentRecordPrompts::class;

    /**
     * @var string
     */
    protected $table = 'records_assignments';

    /**
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'status_id', 'unit_id', 'position_id', 'specialty_id', 'document_id', 'author_id', 'type', 'text', 'updated_at', 'created_at'];

    /**
     * @var string[]
     */
    protected $with = ['position', 'specialty', 'unit'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'type' => AssignmentRecordType::class,
    ];

    /**
     * @var string[]
     */
    protected static array $recordEvents = ['created'];

    protected static function booted(): void
    {
        static::addGlobalScope(new AssignmentRecordScope);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('newsfeed')
            ->setDescriptionForEvent(fn ($event) => "An assignment record has been $event");
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        if ($eventName === 'created') {
            $position = optional($this->position, function (Position $position) {
                return $position->name;
            }) ?? 'No Position Assigned';

            $specialty = optional($this->specialty, function (Specialty $specialty) {
                return $specialty->name;
            }) ?? 'No Specialty Assigned';

            $unit = optional($this->unit, function (Unit $unit) {
                return $unit->name;
            }) ?? 'No Unit Assigned';

            $activity->properties = $activity->properties->put('headline', "An assignment record has been added for {$this->user->name}");
            $activity->properties = $activity->properties->put('text', $this->text);
            $activity->properties = $activity->properties->put('item', "Position: {$position}<br> Specialty: {$specialty}<br> Unit: {$unit}<br>");
        }
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
