<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $rank_id
 * @property int $category_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankCategory whereRankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RankCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class RankCategory extends Pivot
{
    protected $table = 'ranks_categories';

    protected $fillable = [
        'rank_id',
        'category_id',
        'order',
        'created_at',
        'updated_at',
    ];
}
