<?php

namespace App\Models;

use App\Models\Scopes\AwardRecordScope;
use App\Prompts\AwardRecordPrompts;
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
 * App\Models\AwardRecord
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $author
 * @property-read \App\Models\Award|null $award
 * @property-read \App\Models\Document|null $document
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\AwardRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord forAuthor($user)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord forDocument($document)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AwardRecord query()
 *
 * @mixin \Eloquent
 */
class AwardRecord extends Model
{
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasEventPrompts;
    use HasFactory;
    use HasUser;
    use LogsActivity;

    protected string $prompts = AwardRecordPrompts::class;

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'award_id', 'document_id', 'author_id', 'text'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'records_awards';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new AwardRecordScope());
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('newsfeed');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function award()
    {
        return $this->belongsTo(Award::class);
    }
}
