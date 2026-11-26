<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Integrations;

use App\Actions\Integrations\ConnectDiscordServer;
use App\Exceptions\IntegrationConnectionException;
use App\Http\Controllers\App\Integrations\Concerns\CompletesIntegrationInstall;
use App\Services\DiscordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DiscordConnectController
{
    use CompletesIntegrationInstall;

    public function __invoke(Request $request): RedirectResponse
    {
        return $this->completeInstall($request, 'discord', 'Discord', function (string $code): string {
            $guild = DiscordService::exchangeInstallCode($code);

            if (blank($guild)) {
                throw IntegrationConnectionException::authorizationFailed('Discord');
            }

            ConnectDiscordServer::handle($guild['id'], $guild['name']);

            return $guild['name'];
        });
    }
}
