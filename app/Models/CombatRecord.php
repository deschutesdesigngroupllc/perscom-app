<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\SendsModelNotifications;
use App\Contracts\ShouldGenerateNewsfeedItems;
use App\Models\Scopes\CombatRecordScope;
use App\Observers\CombatRecordObserver;
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

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $document_id
 * @property int|null $author_id
 * @property string $text
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
 * @property-read string|null $relative_url
 * @property-read string|null $url
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord author(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord document(\App\Models\Document $document)
 * @method static \Database\Factories\CombatRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord user(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CombatRecord whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(CombatRecordObserver::class)]
#[ScopedBy(CombatRecordScope::class)]
class CombatRecord extends Model implements HasLabel, SendsModelNotifications, ShouldGenerateNewsfeedItems
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

    protected $table = 'records_combat';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'text',
        'created_at',
        'updated_at',
    ];

    public function headlineForNewsfeedItem(): string
    {
        return "A combat record has been added for {$this->user->name}";
    }

    public function textForNewsfeedItem(): ?string
    {
        return $this->text;
    }

    public function itemForNewsfeedItem(): ?string
    {
        return null;
    }

    public function recipientForNewsfeedItem(): ?User
    {
        return $this->user;
    }
}
