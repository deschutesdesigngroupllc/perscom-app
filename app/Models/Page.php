<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Hideable;
use App\Traits\CanBeHidden;
use App\Traits\CanBeOrdered;
use App\Traits\HasIcon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $icon
 * @property string $content
 * @property bool $hidden
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\PageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page hidden()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page visible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Page extends Model implements Hideable, Sortable
{
    use CanBeHidden;
    use CanBeOrdered;
    use HasFactory;
    use HasIcon;

    protected $attributes = [
        'hidden' => false,
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
    ];
}
