<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\SendsModelNotifications;
use App\Contracts\ShouldGenerateNewsfeedItems;
use App\Models\Enums\RankRecordType;
use App\Models\Scopes\RankRecordScope;
use App\Observers\RankRecordObserver;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasComments;
use App\Traits\HasDocument;
use App\Traits\HasLogs;
use App\Traits\HasModelNotifications;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasUser;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $rank_id
 * @property int|null $document_id
 * @property int|null $author_id
 * @property string|null $text
 * @property RankRecordType $type
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
 * @property-read Rank|null $rank
 * @property-read string|null $relative_url
 * @property-read string|null $url
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord author(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord document(\App\Models\Document $document)
 * @method static \Database\Factories\RankRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord user(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord whereRankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankRecord whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(RankRecordObserver::class)]
#[ScopedBy(RankRecordScope::class)]
class RankRecord extends Model implements HasLabel, SendsModelNotifications, ShouldGenerateNewsfeedItems
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
    use HasResourceLabel;
    use HasResourceUrl;
    use HasUser;

    protected $table = 'records_ranks';

    /**
     * @var array
     */
    protected $attributes = [
        'type' => RankRecordType::PROMOTION,
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'rank_id',
        'text',
        'type',
        'created_at',
        'updated_at',
    ];

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function headlineForNewsfeedItem(): string
    {
        return "A rank record has been added for {$this->user->name}";
    }

    public function textForNewsfeedItem(): ?string
    {
        return $this->text;
    }

    public function itemForNewsfeedItem(): ?string
    {
        return "Rank: {$this->rank->name}";
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
            'type' => RankRecordType::class,
        ];
    }
}
