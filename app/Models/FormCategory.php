<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $form_id
 * @property int $category_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FormCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCategory whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class FormCategory extends Pivot
{
    protected $table = 'forms_categories';

    protected $fillable = [
        'form_id',
        'category_id',
        'order',
        'created_at',
        'updated_at',
    ];
}
