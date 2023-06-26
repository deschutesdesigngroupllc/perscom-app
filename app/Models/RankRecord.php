<?php

namespace App\Models;

use App\Models\Scopes\RankRecordScope;
use App\Prompts\RankRecordPrompts;
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
 * App\Models\RankRecord
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Document|null $document
 * @property-read \App\Models\Rank|null $rank
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\RankRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord forAuthor($user)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord forDocument($document)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RankRecord query()
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

    protected string $prompts = RankRecordPrompts::class;

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'rank_id', 'document_id', 'author_id', 'text', 'type'];

    /**
     * Record types
     */
    public const RECORD_RANK_PROMOTION = 0;

    public const RECORD_RANK_DEMOTION = 1;

    /**
     * @var string[]
     */
    protected $with = ['rank'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'records_ranks';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new RankRecordScope);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('newsfeed');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }
}
