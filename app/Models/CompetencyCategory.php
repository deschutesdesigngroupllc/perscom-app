<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Database\Factories\CompetencyCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyCategory query()
 *
 * @mixin \Eloquent
 */
class CompetencyCategory extends Model
{
    /** @use HasFactory<\Database\Factories\CompetencyCategoryFactory> */
    use HasFactory;
}
