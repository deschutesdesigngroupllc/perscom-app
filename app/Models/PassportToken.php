<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token as BaseTokenModel;

/**
 * @property string $id
 * @property int|null $user_id
 * @property string $client_id
 * @property string|null $name
 * @property array<array-key, mixed>|null $scopes
 * @property string|null $token
 * @property bool $revoked
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $expires_at
 * @property-read PassportClient|null $client
 * @property-read RefreshToken|null $refreshToken
 * @property-read User|null $user
 *
 * @method static \Database\Factories\PassportTokenFactory factory($count = null, $state = [])
 * @method static Builder<static>|PassportToken newModelQuery()
 * @method static Builder<static>|PassportToken newQuery()
 * @method static Builder<static>|PassportToken query()
 * @method static Builder<static>|PassportToken whereClientId($value)
 * @method static Builder<static>|PassportToken whereCreatedAt($value)
 * @method static Builder<static>|PassportToken whereExpiresAt($value)
 * @method static Builder<static>|PassportToken whereId($value)
 * @method static Builder<static>|PassportToken whereName($value)
 * @method static Builder<static>|PassportToken whereRevoked($value)
 * @method static Builder<static>|PassportToken whereScopes($value)
 * @method static Builder<static>|PassportToken whereToken($value)
 * @method static Builder<static>|PassportToken whereUpdatedAt($value)
 * @method static Builder<static>|PassportToken whereUserId($value)
 *
 * @mixin \Eloquent
 */
class PassportToken extends BaseTokenModel
{
    use HasFactory;

    protected $attributes = [
        'revoked' => false,
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expires_at' => 'datetime',
        'scopes' => 'array',
        'token' => 'encrypted',
        'revoked' => 'bool',
    ];
}
