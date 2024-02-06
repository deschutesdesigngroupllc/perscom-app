<?php

namespace App\Auth\Providers;

use DateTimeImmutable;
use Exception;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Providers\JWT\Lcobucci;

class CustomJwtProvider extends Lcobucci
{
    /**
     * @throws JWTException
     */
    public function getPerscomSigner(): Signer
    {
        return $this->getSigner();
    }

    /**
     * @return array<string, mixed>
     *
     * @throws TokenInvalidException
     */
    public function decode($token): array
    {
        try {
            $jwt = $this->config->parser()->parse($token);
        } catch (Exception $e) {
            throw new TokenInvalidException('Could not decode token: '.$e->getMessage(), $e->getCode(), $e);
        }

        $perscomSigningKeyConstraint = $this->config->validator()->validate($jwt, new SignedWith($this->signer, $this->getVerificationKey()));
        $tenantSigningKeyConstraint = optional(setting('single_sign_on_key'), function (string $key) use ($jwt) {
            return $this->config->validator()->validate($jwt, new SignedWith($this->signer, InMemory::plainText($key)));
        }) ?? false;

        if (! $perscomSigningKeyConstraint && ! $tenantSigningKeyConstraint) {
            throw new TokenInvalidException('Token signature could not be verified.');
        }

        // @phpstan-ignore-next-line
        return collect($jwt->claims()->allx())->map(function ($claim) {
            if (is_a($claim, DateTimeImmutable::class)) {
                return $claim->getTimestamp();
            }
            if (is_object($claim) && method_exists($claim, 'getValue')) {
                return $claim->getValue();
            }

            return $claim;
        })->toArray();
    }
}
