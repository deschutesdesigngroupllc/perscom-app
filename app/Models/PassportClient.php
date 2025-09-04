<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\PassportClientType;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Laravel\Passport\AuthCode;
use Laravel\Passport\Client as BaseClientModel;
use Laravel\Passport\Database\Factories\ClientFactory;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AuthCode> $authCodes
 * @property-read int|null $auth_codes_count
 * @property-read string|null $plain_secret
 * @property-read Image|null $image
 * @property-read Collection<int, Image> $images
 * @property-read int|null $images_count
 * @property-read Collection<int, PassportToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read User|null $user
 *
 * @method static ClientFactory factory($count = null, $state = [])
 * @method static Builder<static>|PassportClient newModelQuery()
 * @method static Builder<static>|PassportClient newQuery()
 * @method static Builder<static>|PassportClient query()
 * @method static Builder<static>|PassportClient whereCreatedAt($value)
 * @method static Builder<static>|PassportClient whereDescription($value)
 * @method static Builder<static>|PassportClient whereId($value)
 * @method static Builder<static>|PassportClient whereLogout($value)
 * @method static Builder<static>|PassportClient whereName($value)
 * @method static Builder<static>|PassportClient wherePasswordClient($value)
 * @method static Builder<static>|PassportClient wherePersonalAccessClient($value)
 * @method static Builder<static>|PassportClient whereProvider($value)
 * @method static Builder<static>|PassportClient whereRedirect($value)
 * @method static Builder<static>|PassportClient whereRevoked($value)
 * @method static Builder<static>|PassportClient whereScopes($value)
 * @method static Builder<static>|PassportClient whereSecret($value)
 * @method static Builder<static>|PassportClient whereType($value)
 * @method static Builder<static>|PassportClient whereUpdatedAt($value)
 * @method static Builder<static>|PassportClient whereUserId($value)
 *
 * @mixin \Eloquent
 */
class PassportClient extends BaseClientModel
{
    use HasImages;

    /**
     * The system OAuth client that is used to issue API keys. This should
     * never be edited or deleted.
     */
    public const string SYSTEM_PERSONAL_ACCESS_CLIENT = 'Default Personal Access Client';

    /**
     * The system OAuth client that is used to issue password grant API keys. This
     * should never be edited or deleted.
     */
    public const string SYSTEM_PASSWORD_GRANT_CLIENT = 'Default Password Grant Client';

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
