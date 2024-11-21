<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\SendsModelNotifications;
use App\Contracts\ShouldGenerateNewsfeedItems;
use App\Models\Scopes\ServiceRecordScope;
use App\Observers\ServiceRecordObserver;
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
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $document_id
 * @property int|null $author_id
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read User|null $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read Document|null $document
 * @property-read mixed $document_parsed
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ModelNotification> $modelNotifications
 * @property-read int|null $model_notifications_count
 * @property-read string|null $relative_url
 * @property-read string|null $url
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord author(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord document(\App\Models\Document $document)
 * @method static \Database\Factories\ServiceRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord user(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRecord withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy(ServiceRecordObserver::class)]
#[ScopedBy(ServiceRecordScope::class)]
class ServiceRecord extends Model implements HasLabel, SendsModelNotifications, ShouldGenerateNewsfeedItems
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
    use SoftDeletes;

    protected $table = 'records_service';

    protected $fillable = [
        'text',
        'created_at',
        'created_at',
        'deleted_at',
    ];

    public function headlineForNewsfeedItem(): string
    {
        return "A service record has been added for {$this->user->name}";
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
