<?php

declare(strict_types=1);

namespace App\Support\Twig\Extensions;

use App\Models\User;
use App\Settings\IntegrationSettings;
use Firebase\JWT\JWT;
use Illuminate\Container\Attributes\CurrentUser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SsoJwtExtension extends AbstractExtension
{
    public function __construct(
        #[CurrentUser]
        private readonly User $user,
        private readonly IntegrationSettings $settings,
    ) {
        //
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ssoJwt', [$this, 'ssoJwt']),
        ];
    }

    public function ssoJwt(): string
    {
        return $this->generateJwt();
    }

    private function generateJwt(): string
    {
        $now = now();
        $exp = $now->clone()->addMinutes(5);

        $payload = [
            'sub' => $this->user->id,
            'iat' => $now->getTimestamp(),
            'exp' => $exp->getTimestamp(),
            'scopes' => ['*'],
            'tenant' => tenant()->getKey(),
        ];

        return JWT::encode(
            $payload,
            $this->settings->single_sign_on_key,
            'HS256'
        );
    }
}
