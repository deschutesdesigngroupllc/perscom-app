<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Models\Status
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\StatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Status extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'color', 'updated_at', 'created_at'];

    /**
     * @var string[]
     */
    public static $colors = [
        'bg-sky-100 text-sky-600' => 'Blue',
        'bg-gray-100 text-gray-600' => 'Gray',
        'bg-green-100 text-green-600' => 'Green',
        'bg-red-100 text-red-600' => 'Red',
        'bg-white text-black' => 'White',
        'bg-yellow-100 text-yellow-600' => 'Yellow',
    ];

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_statuses');
    }

    public function submissions(): MorphToMany
    {
        return $this->morphedByMany(Submission::class, 'model', 'model_has_statuses');
    }
}
