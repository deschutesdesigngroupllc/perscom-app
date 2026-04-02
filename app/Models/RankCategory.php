<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasRank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $rank_id
 * @property int $category_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Category $category
 * @property-read Rank $rank
 *
 * @method static Builder<static>|RankCategory newModelQuery()
 * @method static Builder<static>|RankCategory newQuery()
 * @method static Builder<static>|RankCategory query()
 * @method static Builder<static>|RankCategory rank(\App\Models\Rank $rank)
 * @method static Builder<static>|RankCategory whereCategoryId($value)
 * @method static Builder<static>|RankCategory whereCreatedAt($value)
 * @method static Builder<static>|RankCategory whereId($value)
 * @method static Builder<static>|RankCategory whereOrder($value)
 * @method static Builder<static>|RankCategory whereRankId($value)
 * @method static Builder<static>|RankCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class RankCategory extends Pivot
{
    use HasRank;

    protected $table = 'ranks_categories';

    protected $fillable = [
        'rank_id',
        'category_id',
        'order',
        'created_at',
        'updated_at',
    ];

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
