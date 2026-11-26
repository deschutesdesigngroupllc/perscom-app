<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $tag_id
 * @property int $form_id
 *
 * @method static Builder<static>|FormTag newModelQuery()
 * @method static Builder<static>|FormTag newQuery()
 * @method static Builder<static>|FormTag query()
 * @method static Builder<static>|FormTag whereFormId($value)
 * @method static Builder<static>|FormTag whereTagId($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
#[Fillable([
    'tag_id',
    'form_id',
])]
#[Table(name: 'forms_tags')]
class FormTag extends Pivot {}
