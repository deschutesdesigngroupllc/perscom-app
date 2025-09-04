<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $competency_id
 * @property int|null $category_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|CompetencyCategory newModelQuery()
 * @method static Builder<static>|CompetencyCategory newQuery()
 * @method static Builder<static>|CompetencyCategory query()
 * @method static Builder<static>|CompetencyCategory whereCategoryId($value)
 * @method static Builder<static>|CompetencyCategory whereCompetencyId($value)
 * @method static Builder<static>|CompetencyCategory whereCreatedAt($value)
 * @method static Builder<static>|CompetencyCategory whereId($value)
 * @method static Builder<static>|CompetencyCategory whereOrder($value)
 * @method static Builder<static>|CompetencyCategory whereUpdatedAt($value)
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
