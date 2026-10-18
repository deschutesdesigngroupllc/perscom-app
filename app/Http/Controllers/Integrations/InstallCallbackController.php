<?php

declare(strict_types=1);

namespace App\Http\Controllers\Integrations;

use App\Models\Tenant;
use App\Services\IntegrationInstallService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Url\Url;

class InstallCallbackController
{
    /**
     * Hands the OAuth response to the organization that started the install, where the code is
     * exchanged and the session nonce is checked.
     */
    public function __invoke(Request $request, string $provider): RedirectResponse
    {
        $payload = IntegrationInstallService::decode($request->query('state'));

        abort_unless($payload !== null && data_get($payload, 'provider') === $provider, 400);

        $path = route(sprintf('app.integrations.%s.connect', $provider), absolute: false);

        $base = config('tenancy.enabled')
            ? Tenant::query()->findOrFail(data_get($payload, 'tenant'))->url
            : config('app.url');

        return redirect()->to(Url::fromString(rtrim((string) $base, '/').$path)
            ->withQueryParameters($request->query->all())
            ->__toString());
    }
}
