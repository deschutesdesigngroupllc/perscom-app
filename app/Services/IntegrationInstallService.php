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
            'origin' => request()->getSchemeAndHttpHost(),
        ]);
    }

    /**
     * Decode the OAuth state into its install payload.
     *
     * @return array{provider: string, tenant: int|string|null, nonce: string, origin?: string}|null
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

    /**
     * Where to send the admin after the provider redirects back: the host they started on, as long as it
     * belongs to the organization, so the session holding the nonce is the one that receives the code.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function returnUrlFor(array $payload, ?Tenant $tenant): string
    {
        $fallback = (string) ($tenant?->url ?? config('app.url'));
        $origin = data_get($payload, 'origin');

        if (blank($origin) || ! $tenant instanceof Tenant) {
            return $fallback;
        }

        $originHost = parse_url((string) $origin, PHP_URL_HOST);
        $allowedHosts = collect([$tenant->custom_url, $tenant->fallback_url, $tenant->url])
            ->filter()
            ->map(fn (string $url): ?string => parse_url($url, PHP_URL_HOST))
            ->filter();

        return $allowedHosts->contains($originHost) ? (string) $origin : $fallback;
    }

    protected static function sessionKey(string $provider): string
    {
        return sprintf('integrations.%s.nonce', $provider);
    }
}
