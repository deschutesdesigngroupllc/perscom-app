<?php

namespace App\Models\Records;

use App\Models\Rank as RankModel;
use App\Models\User;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasAttachments;
    use HasAuthor;
    use HasDocument;
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'rank_id',
        'document_id',
        'author_id',
        'text',
    ];

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
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (Rank $record) {
            if ($record->user) {
                $record->user->rank_id = $record->rank?->id;
                $record->user->save();
            }
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rank()
    {
        return $this->belongsTo(RankModel::class);
    }
}
