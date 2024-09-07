<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $award_id
 * @property int $category_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AwardCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AwardCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AwardCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|AwardCategory whereAwardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AwardCategory extends Pivot
{
    protected $table = 'awards_categories';

    protected $fillable = [
        'award_id',
        'category_id',
        'order',
        'created_at',
        'updated_at',
    ];
}
