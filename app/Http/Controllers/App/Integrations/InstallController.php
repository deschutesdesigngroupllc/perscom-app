<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Integrations;

use App\Services\DiscordService;
use App\Services\IntegrationInstallService;
use App\Services\SlackService;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class InstallController
{
    public function __invoke(string $provider): RedirectResponse
    {
        abort_unless(Auth::user()?->hasRole(Utils::getSuperAdminName()), 403);

        $state = IntegrationInstallService::state($provider);

        return redirect()->away(match ($provider) {
            'discord' => DiscordService::installUrl($state),
            'slack' => SlackService::installUrl($state),
            default => abort(404),
        });
    }
}
