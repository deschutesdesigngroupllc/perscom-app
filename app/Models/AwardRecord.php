<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\SendsModelNotifications;
use App\Contracts\ShouldGenerateNewsfeedItems;
use App\Models\Scopes\AwardRecordScope;
use App\Observers\AwardRecordObserver;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasComments;
use App\Traits\HasCustomFieldData;
use App\Traits\HasDocument;
use App\Traits\HasLogs;
use App\Traits\HasModelNotifications;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
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
 * @property int|null $award_id
 * @property int|null $document_id
 * @property int|null $author_id
 * @property string|null $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @property-read Collection<int, Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read User|null $author
 * @property-read Award|null $award
 * @property-read Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 * @property-read Document|null $document
 * @property-read mixed $document_parsed
 * @property-read string $label
 * @property-read Collection<int, Activity> $logs
 * @property-read int|null $logs_count
 * @property-read Collection<int, ModelNotification> $modelNotifications
 * @property-read int|null $model_notifications_count
 * @property-read string|null $relative_url
 * @property-read string|null $url
 * @property-read User|null $user
 *
 * @method static Builder<static>|AwardRecord author(\App\Models\User $user)
 * @method static Builder<static>|AwardRecord document(\App\Models\Document $document)
 * @method static \Database\Factories\AwardRecordFactory factory($count = null, $state = [])
 * @method static Builder<static>|AwardRecord newModelQuery()
 * @method static Builder<static>|AwardRecord newQuery()
 * @method static Builder<static>|AwardRecord query()
 * @method static Builder<static>|AwardRecord user(\App\Models\User $user)
 * @method static Builder<static>|AwardRecord whereAuthorId($value)
 * @method static Builder<static>|AwardRecord whereAwardId($value)
 * @method static Builder<static>|AwardRecord whereCreatedAt($value)
 * @method static Builder<static>|AwardRecord whereDocumentId($value)
 * @method static Builder<static>|AwardRecord whereId($value)
 * @method static Builder<static>|AwardRecord whereText($value)
 * @method static Builder<static>|AwardRecord whereUpdatedAt($value)
 * @method static Builder<static>|AwardRecord whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(AwardRecordObserver::class)]
#[ScopedBy(AwardRecordScope::class)]
class AwardRecord extends Model implements HasLabel, SendsModelNotifications, ShouldGenerateNewsfeedItems
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasAttachments;
    use HasAuthor;
    use HasComments;
    use HasCustomFieldData;
    use HasDocument;
    use HasFactory;
    use HasLogs;
    use HasModelNotifications;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasUser;

    protected $table = 'records_awards';

    protected $fillable = [
        'id',
        'award_id',
        'text',
        'created_at',
        'updated_at',
    ];

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    public function headlineForNewsfeedItem(): string
    {
        return 'An award record has been added for '.$this->user->name;
    }

    public function textForNewsfeedItem(): ?string
    {
        return $this->text;
    }

    public function itemForNewsfeedItem(): ?string
    {
        return 'Award: '.$this->award->name;
    }

    public function recipientForNewsfeedItem(): ?User
    {
        return $this->user;
    }
}
