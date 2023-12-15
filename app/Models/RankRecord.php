<?php

namespace App\Models;

use App\Models\Enums\RankRecordType;
use App\Models\Scopes\RankRecordScope;
use App\Prompts\RankRecordPrompts;
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
 * App\Models\RankRecord
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $rank_id
 * @property int|null $document_id
 * @property int|null $author_id
 * @property string|null $text
 * @property int $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Document|null $document
 * @property-read \App\Models\Rank|null $rank
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord author(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord document(\App\Models\Document $document)
 * @method static \Database\Factories\RankRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord user(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord whereRankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord whereUserId($value)
 *
 * @mixin \Eloquent
 */
class RankRecord extends Model
{
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasEventPrompts;
    use HasFactory;
    use HasUser;
    use LogsActivity;

    protected static string $prompts = RankRecordPrompts::class;

    /**
     * @var string[]
     */
    protected static array $recordEvents = ['created'];

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'rank_id', 'document_id', 'author_id', 'text', 'type', 'updated_at', 'created_at'];

    /**
     * @var string[]
     */
    protected $with = ['rank'];

    /**
     * @var string
     */
    protected $table = 'records_ranks';

    /**
     * @var string[]
     */
    protected $casts = [
        'type' => RankRecordType::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new RankRecordScope);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('newsfeed')
            ->setDescriptionForEvent(fn ($event) => "A rank record has been $event");
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        if ($eventName === 'created') {
            $activity->properties = $activity->properties->put('headline', "A rank record has been added for {$this->user->name}");
            $activity->properties = $activity->properties->put('text', $this->text);
            $activity->properties = $activity->properties->put('item', "Rank: {$this->rank->name}");
        }
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }
}
