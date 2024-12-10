<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Hideable;
use App\Traits\CanBeHidden;
use App\Traits\CanBeOrdered;
use App\Traits\HasUsers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $order
 * @property bool $hidden
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read UnitSlot $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Unit> $units
 * @property-read int|null $units_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\SlotFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Slot hidden()
 * @method static \Illuminate\Database\Eloquent\Builder|Slot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slot onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Slot ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Slot query()
 * @method static \Illuminate\Database\Eloquent\Builder|Slot visible()
 * @method static \Illuminate\Database\Eloquent\Builder|Slot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slot whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slot whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slot whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slot whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slot whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slot withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Slot withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Slot extends Model implements Hideable, Sortable
{
    use CanBeHidden;
    use CanBeOrdered;
    use HasFactory;
    use HasUsers;
    use SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
    ];

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'units_slots')
            ->using(UnitSlot::class);
    }
}
