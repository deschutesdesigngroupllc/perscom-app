<?php

namespace App\Support\OpenIDConnect\Repositories;

use App\Support\OpenIDConnect\Entities\UserEntity;
use OpenIDConnect\Interfaces\IdentityEntityInterface;
use OpenIDConnect\Repositories\IdentityRepository as BaseIdentityRepository;

class IdentityRepository extends BaseIdentityRepository
{
    public function getByIdentifier(string $identifier): IdentityEntityInterface
    {
        $identityEntity = new UserEntity();
        $identityEntity->setIdentifier($identifier);

        return $identityEntity;
    }
}
