<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\ShouldGenerateNewsfeedItems;
use App\Models\Scopes\ServiceRecordScope;
use App\Observers\ServiceRecordObserver;
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
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $document_id
 * @property int|null $author_id
 * @property string $text
 * @property array<array-key, mixed>|null $data
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
 * @property-read string|null $relative_url
 * @property-read string|null $url
 * @property-read User|null $user
 *
 * @method static Builder<static>|ServiceRecord author(\App\Models\User $user)
 * @method static Builder<static>|ServiceRecord document(\App\Models\Document $document)
 * @method static \Database\Factories\ServiceRecordFactory factory($count = null, $state = [])
 * @method static Builder<static>|ServiceRecord newModelQuery()
 * @method static Builder<static>|ServiceRecord newQuery()
 * @method static Builder<static>|ServiceRecord query()
 * @method static Builder<static>|ServiceRecord user(\App\Models\User $user)
 * @method static Builder<static>|ServiceRecord whereAuthorId($value)
 * @method static Builder<static>|ServiceRecord whereCreatedAt($value)
 * @method static Builder<static>|ServiceRecord whereData($value)
 * @method static Builder<static>|ServiceRecord whereDocumentId($value)
 * @method static Builder<static>|ServiceRecord whereId($value)
 * @method static Builder<static>|ServiceRecord whereText($value)
 * @method static Builder<static>|ServiceRecord whereUpdatedAt($value)
 * @method static Builder<static>|ServiceRecord whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(ServiceRecordObserver::class)]
#[ScopedBy(ServiceRecordScope::class)]
class ServiceRecord extends Model implements HasLabel, ShouldGenerateNewsfeedItems
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

    protected $table = 'records_service';

    protected $fillable = [
        'id',
        'text',
        'updated_at',
        'created_at',
    ];

    public function headlineForNewsfeedItem(): string
    {
        return 'A service record has been added for '.$this->user->name;
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
