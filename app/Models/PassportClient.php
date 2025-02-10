<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\PassportClientType;
use App\Traits\HasImages;
use Laravel\Passport\Client as BaseClientModel;

/**
 * @property string $id
 * @property int|null $user_id
 * @property string $name
 * @property string|null $description
 * @property PassportClientType|null $type
 * @property string|null $secret
 * @property string|null $provider
 * @property string $redirect
 * @property string|null $logout
 * @property array<array-key, mixed>|null $scopes
 * @property bool $personal_access_client
 * @property bool $password_client
 * @property bool $revoked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\AuthCode> $authCodes
 * @property-read int|null $auth_codes_count
 * @property-read string|null $plain_secret
 * @property-read Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Image> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PassportToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read User|null $user
 *
 * @method static \Laravel\Passport\Database\Factories\ClientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereLogout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient wherePasswordClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient wherePersonalAccessClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereRedirect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereRevoked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereScopes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PassportClient whereUserId($value)
 *
 * @mixin \Eloquent
 */
class PassportClient extends BaseClientModel
{
    use HasImages;

    protected $attributes = [
        'personal_access_client' => false,
        'password_client' => false,
        'redirect' => 'http://your.redirect.path',
        'revoked' => false,
    ];

    public function hasScope($scope): bool
    {
        /**
         * Passport clients do not yet support a wildcard "*" all scopes. Passport
         * access tokens currently do.
         */
        if (is_array($this->scopes) && in_array('*', $this->scopes)) {
            return true;
        }

        return parent::hasScope($scope);
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'type' => PassportClientType::class,
        ];
    }
}
