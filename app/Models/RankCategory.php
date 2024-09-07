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
 * @method static \Illuminate\Database\Eloquent\Builder|RankCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RankCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RankCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|RankCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankCategory whereRankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RankCategory whereUpdatedAt($value)
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
