<?php

namespace App\Entities;

use App\Models\User;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use OpenIDConnect\Interfaces\IdentityEntityInterface;

class UserEntity implements IdentityEntityInterface
{
    use EntityTrait;

    protected ?User $user;

    /**
     * @param  mixed  $identifier
     */
    public function setIdentifier($identifier): void
    {
        $this->identifier = $identifier;
        $this->user = User::findOrFail($identifier);
    }

    public function getClaims(): array
    {
        return $this->user->getJWTCustomClaims();
    }
}
