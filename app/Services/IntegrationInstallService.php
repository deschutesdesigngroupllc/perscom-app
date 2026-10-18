<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/**
 * Discord and Slack send every organization back to one central redirect URL. The OAuth state
 * carries the organization so the central callback can hand off to it, and a session nonce proves
 * the install was started by the administrator who finishes it.
 */
class IntegrationInstallService
{
    public static function state(string $provider): string
    {
        $nonce = Str::random(40);

        Session::put(IntegrationInstallService::sessionKey($provider), $nonce);

        /** @var ?Tenant $tenant */
        $tenant = tenant();

        return Crypt::encrypt([
            'provider' => $provider,
            'tenant' => $tenant?->getTenantKey(),
            'nonce' => $nonce,
        ]);
    }

    /**
     * @return array{provider: string, tenant: int|string|null, nonce: string}|null
     */
    public static function decode(?string $state): ?array
    {
        if (blank($state)) {
            return null;
        }

        try {
            $payload = Crypt::decrypt($state);
        } catch (DecryptException) {
            return null;
        }

        return is_array($payload) && filled(data_get($payload, 'provider')) && filled(data_get($payload, 'nonce'))
            ? $payload
            : null;
    }

    public static function verify(string $provider, ?string $state): bool
    {
        $payload = IntegrationInstallService::decode($state);
        $nonce = Session::pull(IntegrationInstallService::sessionKey($provider));

        /** @var ?Tenant $tenant */
        $tenant = tenant();

        return $payload !== null
            && filled($nonce)
            && data_get($payload, 'provider') === $provider
            && hash_equals((string) $nonce, (string) data_get($payload, 'nonce'))
            && (string) data_get($payload, 'tenant') === (string) $tenant?->getTenantKey();
    }

    protected static function sessionKey(string $provider): string
    {
        return sprintf('integrations.%s.nonce', $provider);
    }
}
