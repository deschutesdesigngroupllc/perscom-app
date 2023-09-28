<?php

namespace App\Models;

use App\Traits\HasImages;
use Laravel\Passport\Client as BaseClientModel;

/**
 * App\Models\PassportClient
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\AuthCode> $authCodes
 * @property-read int|null $auth_codes_count
 * @property-read string|null $plain_secret
 * @property-write mixed $secret
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PassportToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Laravel\Passport\Database\Factories\ClientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient query()
 *
 * @mixin \Eloquent
 */
class PassportClient extends BaseClientModel
{
    use HasImages;

    /**
     * @var false[]
     */
    protected $attributes = [
        'personal_access_client' => false,
        'password_client' => false,
        'redirect' => 'http://your.redirect.path',
        'revoked' => false,
    ];
}
