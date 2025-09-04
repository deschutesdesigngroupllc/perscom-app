<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\SendsModelNotifications;
use App\Contracts\ShouldGenerateNewsfeedItems;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Scopes\AssignmentRecordScope;
use App\Observers\AssignmentRecordObserver;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasComments;
use App\Traits\HasDocument;
use App\Traits\HasLogs;
use App\Traits\HasModelNotifications;
use App\Traits\HasPosition;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasSpecialty;
use App\Traits\HasStatus;
use App\Traits\HasUnit;
use App\Traits\HasUser;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $status_id
 * @property int|null $unit_id
 * @property int|null $position_id
 * @property int|null $specialty_id
 * @property int|null $unit_slot_id
 * @property int|null $document_id
 * @property int|null $author_id
 * @property AssignmentRecordType|null $type
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
 * @property-read Document|null $document
 * @property-read mixed $document_parsed
 * @property-read string $label
 * @property-read Collection<int, Activity> $logs
 * @property-read int|null $logs_count
 * @property-read Collection<int, ModelNotification> $modelNotifications
 * @property-read int|null $model_notifications_count
 * @property-read Position|null $position
 * @property-read string|null $relative_url
 * @property-read Specialty|null $specialty
 * @property-read Status|null $status
 * @property-read Unit|null $unit
 * @property-read UnitSlot|null $unit_slot
 * @property-read string|null $url
 * @property-read User|null $user
 *
 * @method static Builder<static>|AssignmentRecord author(\App\Models\User $user)
 * @method static Builder<static>|AssignmentRecord document(\App\Models\Document $document)
 * @method static \Database\Factories\AssignmentRecordFactory factory($count = null, $state = [])
 * @method static Builder<static>|AssignmentRecord newModelQuery()
 * @method static Builder<static>|AssignmentRecord newQuery()
 * @method static Builder<static>|AssignmentRecord position(\App\Models\Position $position)
 * @method static Builder<static>|AssignmentRecord query()
 * @method static Builder<static>|AssignmentRecord specialty(\App\Models\Specialty $specialty)
 * @method static Builder<static>|AssignmentRecord status(\App\Models\Status $status)
 * @method static Builder<static>|AssignmentRecord unit(\App\Models\Unit $unit)
 * @method static Builder<static>|AssignmentRecord user(\App\Models\User $user)
 * @method static Builder<static>|AssignmentRecord whereAuthorId($value)
 * @method static Builder<static>|AssignmentRecord whereCreatedAt($value)
 * @method static Builder<static>|AssignmentRecord whereDocumentId($value)
 * @method static Builder<static>|AssignmentRecord whereId($value)
 * @method static Builder<static>|AssignmentRecord wherePositionId($value)
 * @method static Builder<static>|AssignmentRecord whereSpecialtyId($value)
 * @method static Builder<static>|AssignmentRecord whereStatusId($value)
 * @method static Builder<static>|AssignmentRecord whereText($value)
 * @method static Builder<static>|AssignmentRecord whereType($value)
 * @method static Builder<static>|AssignmentRecord whereUnitId($value)
 * @method static Builder<static>|AssignmentRecord whereUnitSlotId($value)
 * @method static Builder<static>|AssignmentRecord whereUpdatedAt($value)
 * @method static Builder<static>|AssignmentRecord whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(AssignmentRecordObserver::class)]
#[ScopedBy(AssignmentRecordScope::class)]
class AssignmentRecord extends Model implements HasLabel, SendsModelNotifications, ShouldGenerateNewsfeedItems
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasAttachments;
    use HasAuthor;
    use HasComments;
    use HasDocument;
    use HasFactory;
    use HasLogs;
    use HasModelNotifications;
    use HasPosition;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasSpecialty;
    use HasStatus;
    use HasUnit;
    use HasUser;

    protected $table = 'records_assignments';

    protected $attributes = [
        'type' => AssignmentRecordType::PRIMARY,
    ];

    protected $fillable = [
        'unit_slot_id',
        'type',
        'text',
        'created_at',
        'updated_at',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (AssignmentRecord $record): void {
            if (filled($record->unit_slot_id)) {
                $record->forceFill([
                    'unit_id' => $record->unit_slot->unit_id ?? null,
                    'position_id' => $record->unit_slot->slot->position_id ?? null,
                    'specialty_id' => $record->unit_slot->slot->specialty_id ?? null,
                ]);
            }
        });
    }

    public function headlineForNewsfeedItem(): string
    {
        return "An assignment record has been added for {$this->user->name}";
    }

    public function textForNewsfeedItem(): ?string
    {
        return $this->text;
    }

    public function itemForNewsfeedItem(): ?string
    {
        $position = optional($this->position, fn (Position $position) => $position->name) ?? 'No Position Assigned';

        $specialty = optional($this->specialty, fn (Specialty $specialty) => $specialty->name) ?? 'No Specialty Assigned';

        $unit = optional($this->unit, fn (Unit $unit) => $unit->name) ?? 'No Unit Assigned';

        $status = optional($this->status, fn (Status $status) => $status->name) ?? 'No Status Assigned';

        return "Position: $position<br> Specialty: $specialty<br> Unit: $unit<br>Status: $status<br>";
    }

    public function recipientForNewsfeedItem(): ?User
    {
        return $this->user;
    }

    /**
     * @return BelongsTo<UnitSlot, $this>
     */
    public function unit_slot(): BelongsTo
    {
        return $this->belongsTo(UnitSlot::class);
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'type' => AssignmentRecordType::class,
        ];
    }
}
