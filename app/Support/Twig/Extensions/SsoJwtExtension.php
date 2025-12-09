<?php

declare(strict_types=1);

namespace App\Support\Twig\Extensions;

use App\Models\User;
use App\Settings\IntegrationSettings;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;
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
        /** @var JWTGuard $guard */
        $guard = Auth::guard('jwt');

        /** @var User $user */
        $user = Auth::guard('web')->user();

        return $guard->claims([
            'scopes' => '*',
        ])->login($user);
    }
}
