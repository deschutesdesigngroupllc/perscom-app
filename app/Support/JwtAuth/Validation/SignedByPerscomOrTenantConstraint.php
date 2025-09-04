<?php

declare(strict_types=1);

namespace App\Support\JwtAuth\Validation;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\ConstraintViolation;

readonly class SignedByPerscomOrTenantConstraint implements Constraint
{
    public function __construct(
        private Signer $appSigner,
        private Key $appKey,
        private Signer $tenantSigner,
        private Key $tenantKey
    ) {}

    public function assert(Token $token): void
    {
        $signedByApp = rescue(fn (): bool => $this->signedByApp($token), false, false);
        $signedByTenant = rescue(fn (): bool => $this->signedByTenant($token), false, false);

        if (! $signedByApp && ! $signedByTenant) {
            throw new ConstraintViolation('The provided token was not signed by a valid signer.');
        }
    }

    private function signedByApp(Token $token): bool
    {
        $constraint = new SignedWith($this->appSigner, $this->appKey);
        $constraint->assert($token);

        return true;
    }

    private function signedByTenant(Token $token): bool
    {
        $constraint = new SignedWith($this->tenantSigner, $this->tenantKey);
        $constraint->assert($token);

        return true;
    }
}
