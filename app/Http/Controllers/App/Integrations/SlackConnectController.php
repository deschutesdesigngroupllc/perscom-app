<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Integrations;

use App\Actions\Integrations\ConnectSlackWorkspace;
use App\Exceptions\IntegrationConnectionException;
use App\Http\Controllers\App\Integrations\Concerns\CompletesIntegrationInstall;
use App\Services\SlackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SlackConnectController
{
    use CompletesIntegrationInstall;

    public function __invoke(Request $request): RedirectResponse
    {
        return $this->completeInstall($request, 'slack', 'Slack', function (string $code): string {
            $workspace = SlackService::exchangeInstallCode($code);

            if (blank($workspace)) {
                throw IntegrationConnectionException::authorizationFailed('Slack');
            }

            ConnectSlackWorkspace::handle($workspace['team_id'], $workspace['team_name'], $workspace['bot_token']);

            return $workspace['team_name'];
        });
    }
}
