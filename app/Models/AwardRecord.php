<?php

namespace App\Models;

use App\Models\Scopes\AwardRecordScope;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasDocument;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwardRecord extends Model
{
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasFactory;
    use HasUser;

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
        static::addGlobalScope(new AwardRecordScope);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function award()
    {
        return $this->belongsTo(Award::class);
    }
}
