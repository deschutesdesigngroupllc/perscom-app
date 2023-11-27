<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\Token as BaseTokenModel;

/**
 * App\Models\PassportToken
 *
 * @property string $id
 * @property int|null $user_id
 * @property string $client_id
 * @property string|null $name
 * @property array|null $scopes
 * @property string|null $token
 * @property bool $revoked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property-read \App\Models\PassportClient|null $client
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken whereRevoked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken whereScopes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportToken whereUserId($value)
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
