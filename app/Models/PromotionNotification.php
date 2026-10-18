<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $rank_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Rank $rank
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionNotification whereRankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionNotification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromotionNotification whereUserId($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class PromotionNotification extends Model
{
    protected $fillable = [
        'user_id',
        'rank_id',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Rank, $this>
     */
    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }
}
