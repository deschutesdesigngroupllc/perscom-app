<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $form_id
 * @property int $category_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Category $category
 * @property-read Form $form
 *
 * @method static Builder<static>|FormCategory newModelQuery()
 * @method static Builder<static>|FormCategory newQuery()
 * @method static Builder<static>|FormCategory query()
 * @method static Builder<static>|FormCategory whereCategoryId($value)
 * @method static Builder<static>|FormCategory whereCreatedAt($value)
 * @method static Builder<static>|FormCategory whereFormId($value)
 * @method static Builder<static>|FormCategory whereId($value)
 * @method static Builder<static>|FormCategory whereOrder($value)
 * @method static Builder<static>|FormCategory whereUpdatedAt($value)
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

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
