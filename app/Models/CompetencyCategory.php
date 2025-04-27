<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int|null $competency_id
 * @property int|null $category_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyCategory whereCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class CompetencyCategory extends Pivot
{
    protected $table = 'competencies_categories';

    protected $fillable = [
        'competency_id',
        'category_id',
        'order',
        'created_at',
        'updated_at',
    ];
}
