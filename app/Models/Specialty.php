<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Specialty
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssignmentRecord> $assignment_records
 * @property-read int|null $assignment_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\SpecialtyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Specialty query()
 * @mixin \Eloquent
 */
class Specialty extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'abbreviation', 'description'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function assignment_records()
    {
        return $this->hasMany(AssignmentRecord::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
