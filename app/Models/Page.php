<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Hideable;
use App\Traits\CanBeHidden;
use App\Traits\CanBeOrdered;
use App\Traits\ClearsResponseCache;
use App\Traits\HasIcon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Database\Factories\PageFactory factory($count = null, $state = [])
 * @method static Builder<static>|Page hidden()
 * @method static Builder<static>|Page newModelQuery()
 * @method static Builder<static>|Page newQuery()
 * @method static Builder<static>|Page ordered(string $direction = 'asc')
 * @method static Builder<static>|Page query()
 * @method static Builder<static>|Page visible()
 * @method static Builder<static>|Page whereContent($value)
 * @method static Builder<static>|Page whereCreatedAt($value)
 * @method static Builder<static>|Page whereDescription($value)
 * @method static Builder<static>|Page whereHidden($value)
 * @method static Builder<static>|Page whereIcon($value)
 * @method static Builder<static>|Page whereId($value)
 * @method static Builder<static>|Page whereName($value)
 * @method static Builder<static>|Page whereOrder($value)
 * @method static Builder<static>|Page whereSlug($value)
 * @method static Builder<static>|Page whereUpdatedAt($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class Page extends Model implements Hideable, Sortable
{
    use CanBeHidden;
    use CanBeOrdered;
    use ClearsResponseCache;
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
