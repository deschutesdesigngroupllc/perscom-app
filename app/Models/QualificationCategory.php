<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $qualification_id
 * @property int $category_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationCategory whereQualificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QualificationCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class QualificationCategory extends Pivot
{
    protected $table = 'qualifications_categories';

    protected $fillable = [
        'qualification_id',
        'category_id',
        'order',
        'created_at',
        'updated_at',
    ];
}
