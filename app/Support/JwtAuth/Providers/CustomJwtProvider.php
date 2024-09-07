<?php

declare(strict_types=1);

namespace App\Support\JwtAuth\Providers;

use App\Settings\IntegrationSettings;
use App\Support\JwtAuth\Validation\SignedByPerscomOrTenantConstraint;
use JetBrains\PhpStorm\NoReturn;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use PHPOpenSourceSaver\JWTAuth\Providers\JWT\Lcobucci;

class CustomJwtProvider extends Lcobucci
{
    #[NoReturn]
    public function __construct(protected ?string $secret = null)
    {
        parent::__construct($secret ?? config('jwt.secret'), config('jwt.algo'), config('jwt.keys'));

        /** @var IntegrationSettings $settings */
        $settings = app(IntegrationSettings::class);

        $this->config->setValidationConstraints(new SignedByPerscomOrTenantConstraint(
            perscomSigner: $this->signer,
            perscomKey: $this->getVerificationKey(),
            passportSigner: new Signer\Rsa\Sha256,
            passportKey: InMemory::file(storage_path('oauth-public.key')),
            tenantSigner: new Signer\Hmac\Sha256,
            tenantKey: InMemory::plainText($settings->single_sign_on_key)
        ));
    }

    public function generateConfig() {}
}
