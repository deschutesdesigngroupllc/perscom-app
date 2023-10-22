<?php

namespace App\Models;

use App\Models\Scopes\AssignmentRecordScope;
use App\Prompts\AssignmentRecordPrompts;
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
 * App\Models\AssignmentRecord
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Document|null $document
 * @property-read \App\Models\Position|null $position
 * @property-read \App\Models\Specialty|null $specialty
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
 *
 * @mixin \Eloquent
 */
class AssignmentRecord extends Model
{
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasEventPrompts;
    use HasFactory;
    use HasUser;
    use LogsActivity;

    protected static string $prompts = AssignmentRecordPrompts::class;

    /**
     * @var string[]
     */
    protected static array $recordEvents = ['created'];

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'unit_id', 'secondary_unit_ids', 'position_id', 'secondary_position_ids', 'specialty_id', 'secondary_specialty_ids', 'document_id', 'author_id', 'text'];

    /**
     * @var string[]
     */
    protected $with = ['position', 'specialty', 'unit'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'secondary_position_ids' => 'json',
        'secondary_specialty_ids' => 'json',
        'secondary_unit_ids' => 'json',
    ];

    /**
     * @var string
     */
    protected $table = 'records_assignments';

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

    /**
     * @return void
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName === 'created') {
            $activity->properties = $activity->properties->put('headline', "An assignment record has been added for {$this->user->name}");
            $activity->properties = $activity->properties->put('text', $this->text);
            $activity->properties = $activity->properties->put('item', "Position: {$this->position->name}<br> Specialty: {$this->specialty->name}<br> Unit: {$this->unit->name}<br>");
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
