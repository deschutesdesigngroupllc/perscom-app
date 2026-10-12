<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Integrations;

use App\Actions\Integrations\ConnectDiscordServer;
use App\Exceptions\IntegrationConnectionException;
use App\Filament\App\Clusters\Settings\Pages\Integration;
use App\Services\DiscordService;
use App\Services\IntegrationInstallService;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscordConnectController
{
    public function __invoke(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->hasRole(Utils::getSuperAdminName()), 403);
        abort_unless(IntegrationInstallService::verify('discord', $request->query('state')), 403);

        try {
            $guild = filled($request->query('code'))
                ? DiscordService::exchangeInstallCode((string) $request->query('code'))
                : null;

            throw_unless($guild, IntegrationConnectionException::authorizationFailed('Discord'));

            ConnectDiscordServer::handle($guild['id'], $guild['name']);

            Notification::make()
                ->success()
                ->title('Discord connected')
                ->body(sprintf('PERSCOM is now connected to %s. Choose a channel for notifications.', $guild['name']))
                ->send();
        } catch (IntegrationConnectionException $exception) {
            Notification::make()
                ->danger()
                ->title('Discord was not connected')
                ->body($exception->getMessage())
                ->send();
        }

        return redirect()->to(Integration::getUrl(['tab' => 'discord::data::tab']));
    }
}
