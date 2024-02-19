<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Spatie\Url\Url;

class FeatureOsService
{
    public static function ssoRedirect(string $url): string
    {
        if (! $token = self::generateJwt()) {
            return $url;
        }

        return Url::fromString($url)->withQueryParameter('sso_token', $token)->__toString();
    }

    public static function generateJwt(): ?string
    {
        if (! Auth::check() || ! config('services.featureos.sso_key')) {
            return null;
        }

        $user = Auth::user();

        return JWT::encode([
            'email' => $user->email,
            'name' => $user->name,
            'add_as_customer' => true,
            'avatar' => $user->profile_photo_url,
            'custom_fields' => [
                'Tenant' => tenant('name'),
            ],
        ], config('services.featureos.sso_key'), 'HS256');
    }
}
