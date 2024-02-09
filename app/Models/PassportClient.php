<?php

namespace App\Models;

use App\Traits\HasImages;
use Laravel\Passport\Client as BaseClientModel;

/**
 * App\Models\PassportClient
 *
 * @property string $id
 * @property int|null $user_id
 * @property string $name
 * @property string|null $description
 * @property string|null $type
 * @property string|null $secret
 * @property string|null $provider
 * @property string $redirect
 * @property string|null $logout
 * @property array|null $scopes
 * @property bool $personal_access_client
 * @property bool $password_client
 * @property bool $revoked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\AuthCode> $authCodes
 * @property-read int|null $auth_codes_count
 * @property-read array|null $grant_types
 * @property-read string|null $plain_secret
 * @property-read \App\Models\Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PassportToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\User|null $user
 *
 * @method static \Laravel\Passport\Database\Factories\ClientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient query()
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereLogout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient wherePasswordClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient wherePersonalAccessClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereRedirect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereRevoked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereScopes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PassportClient whereUserId($value)
 *
 * @mixin \Eloquent
 */
class PassportClient extends BaseClientModel
{
    use HasImages;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'personal_access_client' => false,
        'password_client' => false,
        'redirect' => 'http://your.redirect.path',
        'revoked' => false,
    ];
}
