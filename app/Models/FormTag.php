<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $tag_id
 * @property int $form_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FormTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormTag whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormTag whereTagId($value)
 *
 * @mixin \Eloquent
 */
class FormTag extends Pivot
{
    protected $table = 'forms_tags';

    protected $fillable = [
        'tag_id',
        'form_id',
    ];
}
