<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $position_id
 * @property int $category_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Category $category
 * @property-read Position $position
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionCategory wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PositionCategory extends Pivot
{
    protected $table = 'positions_categories';

    protected $fillable = [
        'position_id',
        'category_id',
        'order',
        'created_at',
        'updated_at',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
