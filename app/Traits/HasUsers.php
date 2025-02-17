<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasUsers
{
    /**
     * @return HasMany<User, TModel>
     */
    public function users(): HasMany
    {
        /** @var TModel $this */
        return $this->hasMany(User::class);
    }
}
