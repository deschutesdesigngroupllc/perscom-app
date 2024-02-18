<?php

namespace App\Support\OpenIDConnect\Entities;

use App\Models\User;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use OpenIDConnect\Interfaces\IdentityEntityInterface;

class UserEntity implements IdentityEntityInterface
{
    use EntityTrait;

    protected ?User $user;

    public function setIdentifier(mixed $identifier): void
    {
        $this->identifier = $identifier;
        $this->user = User::findOrFail($identifier);
    }

    public function getClaims(): array
    {
        return $this->user->getJWTCustomClaims();
    }
}
