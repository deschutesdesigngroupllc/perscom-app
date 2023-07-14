<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\Token as BaseTokenModel;

/**
 * App\Models\PassportToken
 *
 * @property-read \App\Models\PassportClient|null $client
 *
 * @method static \Database\Factories\PassportTokenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken query()
 *
 * @mixin \Eloquent
 */
class PassportToken extends BaseTokenModel
{
    use HasFactory;

    /**
     * @var false[]
     */
    protected $attributes = [
        'revoked' => false,
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expires_at' => 'datetime',
        'scopes' => 'array',
        'revoked' => 'bool',
    ];
}
