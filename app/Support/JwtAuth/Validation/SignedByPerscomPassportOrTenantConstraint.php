<?php

declare(strict_types=1);

namespace App\Support\JwtAuth\Validation;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

readonly class SignedByPerscomPassportOrTenantConstraint implements Constraint
{
    public function __construct(
        private Signer $perscomSigner,
        private Signer\Key $perscomKey,
        private Signer $passportSigner,
        private Signer\Key $passportKey,
        private Signer $tenantSigner,
        private Signer\Key $tenantKey
    ) {}

    public function assert(Token $token): void
    {
        $signedByPerscom = rescue(fn () => $this->signedByPerscom($token), false, false);
        $signedByPassport = rescue(fn () => $this->signedByPassport($token), false, false);
        $signedByTenant = rescue(fn () => $this->signedByTenant($token), false, false);

        $tenantClaim = $token->claims()->get('tenant');

        if (($signedByPassport || $signedByPerscom) && blank($tenantClaim)) {
            throw new ConstraintViolation('The provided token does not contain the tenant claim.');
        }

        if (($signedByPassport || $signedByPerscom) && (string) $tenantClaim !== (string) tenant()->getTenantKey()) {
            throw new ConstraintViolation('The tenant claim in the provided token does not match the requested tenant.');
        }

        if (! $signedByPerscom && ! $signedByTenant && ! $signedByPassport) {
            throw new ConstraintViolation('The provided token was not signed by a valid signer.');
        }
    }

    private function signedByPerscom(Token $token): bool
    {
        $constraint = new Constraint\SignedWith($this->perscomSigner, $this->perscomKey);
        $constraint->assert($token);

        return true;
    }

    private function signedByPassport(Token $token): bool
    {
        $constraint = new Constraint\SignedWith($this->passportSigner, $this->passportKey);
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
