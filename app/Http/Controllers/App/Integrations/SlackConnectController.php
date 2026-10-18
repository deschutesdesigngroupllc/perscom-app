<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Integrations;

use App\Actions\Integrations\ConnectSlackWorkspace;
use App\Exceptions\IntegrationConnectionException;
use App\Filament\App\Clusters\Settings\Pages\Integration;
use App\Services\IntegrationInstallService;
use App\Services\SlackService;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SlackConnectController
{
    public function __invoke(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->hasRole(Utils::getSuperAdminName()), 403);
        abort_unless(IntegrationInstallService::verify('slack', $request->query('state')), 403);

        try {
            $workspace = filled($request->query('code'))
                ? SlackService::exchangeInstallCode((string) $request->query('code'))
                : null;

            throw_unless($workspace, IntegrationConnectionException::authorizationFailed('Slack'));

            ConnectSlackWorkspace::handle($workspace['team_id'], $workspace['team_name'], $workspace['bot_token']);

            Notification::make()
                ->success()
                ->title('Slack connected')
                ->body(sprintf('PERSCOM is now connected to %s. Choose a channel for notifications.', $workspace['team_name']))
                ->send();
        } catch (IntegrationConnectionException $integrationConnectionException) {
            Notification::make()
                ->danger()
                ->title('Slack was not connected')
                ->body($integrationConnectionException->getMessage())
                ->send();
        }

        return redirect()->to(Integration::getUrl(['tab' => 'slack::data::tab']));
    }
}
