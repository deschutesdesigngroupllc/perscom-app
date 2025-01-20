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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read User|null $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 * @property-read Document|null $document
 * @property-read mixed $document_parsed
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Activity> $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ModelNotification> $modelNotifications
 * @property-read int|null $model_notifications_count
 * @property-read Position|null $position
 * @property-read string|null $relative_url
 * @property-read Specialty|null $specialty
 * @property-read Status|null $status
 * @property-read Unit|null $unit
 * @property-read string|null $url
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord author(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord document(\App\Models\Document $document)
 * @method static \Database\Factories\AssignmentRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord position(\App\Models\Position $position)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord specialty(\App\Models\Specialty $specialty)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord status(\App\Models\Status $status)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentRecord unit(\App\Models\Unit $unit)
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

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'status_id',
        'unit_id',
        'position_id',
        'specialty_id',
        'type',
        'text',
        'created_at',
        'updated_at',
    ];

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
        $position = optional($this->position, function (Position $position) {
            return $position->name;
        }) ?? 'No Position Assigned';

        $specialty = optional($this->specialty, function (Specialty $specialty) {
            return $specialty->name;
        }) ?? 'No Specialty Assigned';

        $unit = optional($this->unit, function (Unit $unit) {
            return $unit->name;
        }) ?? 'No Unit Assigned';

        $status = optional($this->status, function (Status $status) {
            return $status->name;
        }) ?? 'No Status Assigned';

        return "Position: $position<br> Specialty: $specialty<br> Unit: $unit<br>Status: $status<br>";
    }

    public function recipientForNewsfeedItem(): ?User
    {
        return $this->user;
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
