<?php

namespace App\Models;

use App\Models\Scopes\ServiceRecordScope;
use App\Prompts\ServiceRecordPrompts;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasDocument;
use App\Traits\HasEventPrompts;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\ServiceRecord
 *
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
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Document|null $document
 * @property-read \App\Models\User|null $user
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
class ServiceRecord extends Model
{
    use ClearsResponseCache;
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasEventPrompts;
    use HasFactory;
    use HasUser;
    use LogsActivity;
    use SoftDeletes;

    public static string $prompts = ServiceRecordPrompts::class;

    /**
     * @var string
     */
    protected $table = 'records_service';

    /**
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'document_id', 'author_id', 'text', 'updated_at', 'created_at'];

    /**
     * @var string[]
     */
    protected $with = ['document'];

    /**
     * @var string[]
     */
    protected static array $recordEvents = ['created'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ServiceRecordScope);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('newsfeed')
            ->setDescriptionForEvent(fn ($event) => "A service record has been $event");
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        if ($eventName === 'created') {
            $activity->properties = $activity->properties->put('headline', "A service record has been added for {$this->user->name}");
            $activity->properties = $activity->properties->put('text', $this->text);
        }
    }
}
