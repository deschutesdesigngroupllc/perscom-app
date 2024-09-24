<?php

declare(strict_types=1);

namespace App\Support\JwtAuth\Validation;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

readonly class SignedByPerscomOrTenantConstraint implements Constraint
{
    public function __construct(
        private Signer $perscomSigner,
        private Signer\Key $perscomKey,
        private Signer $tenantSigner,
        private Signer\Key $tenantKey
    ) {}

    public function assert(Token $token): void
    {
        $signedByPerscom = rescue(fn () => $this->signedByPerscom($token), false, false);
        $signedByTenant = rescue(fn () => $this->signedByTenant($token), false, false);

        if (! $signedByPerscom && ! $signedByTenant) {
            throw new ConstraintViolation('The provided token was not signed by a valid signer.');
        }
    }

    private function signedByPerscom(Token $token): bool
    {
        $constraint = new Constraint\SignedWith($this->perscomSigner, $this->perscomKey);
        $constraint->assert($token);

        return true;
    }

    private function signedByTenant(Token $token): bool
    {
        $constraint = new Constraint\SignedWith($this->tenantSigner, $this->tenantKey);
        $constraint->assert($token);

        return true;
    }
}
