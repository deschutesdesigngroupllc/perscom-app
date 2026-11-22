<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Integrations\Concerns;

use App\Exceptions\IntegrationConnectionException;
use App\Filament\App\Clusters\Integrations\Pages\ConnectedServices;
use App\Filament\App\Pages\Dashboard;
use App\Services\IntegrationInstallService;
use BezhanSalleh\FilamentShield\Support\Utils;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Finishes an OAuth install for Discord or Slack. Every outcome lands the admin back on the
 * Integrations page with a message that says what happened and what to do, never a bare error page.
 */
trait CompletesIntegrationInstall
{
    /**
     * @param  Closure(string $code): string  $connect  exchanges the code, connects, and returns the connected server or workspace name
     */
    protected function completeInstall(Request $request, string $provider, string $label, Closure $connect): RedirectResponse
    {
        $back = redirect()->to(ConnectedServices::getUrl(['tab' => sprintf('%s::data::tab', $provider)]));

        if (! Auth::user()?->hasRole(Utils::getSuperAdminName())) {
            $this->notifyFailure($label, sprintf('Only the organization owner can connect %s.', $label));

            return redirect()->to(Dashboard::getUrl());
        }

        $stateIsValid = IntegrationInstallService::verify($provider, $request->query('state'));

        if (filled($request->query('error'))) {
            $error = (string) $request->query('error');

            Log::warning(sprintf('%s install was not authorized', $label), ['error' => $error, 'error_description' => $request->query('error_description')]);

            $this->notifyFailure($label, $error === 'access_denied'
                ? sprintf('You cancelled the authorization in %s. Click Connect when you\'re ready.', $label)
                : sprintf('%s returned an error: %s', $label, Str::limit(strip_tags((string) ($request->query('error_description') ?: $error)), 200)));

            return $back;
        }

        if (! $stateIsValid) {
            $this->notifyFailure($label, 'This connection link expired or was opened in a different browser. Click Connect again.');

            return $back;
        }

        try {
            throw_if(blank($request->query('code')), IntegrationConnectionException::authorizationFailed($label));

            $name = $connect((string) $request->query('code'));

            Notification::make()
                ->success()
                ->title(sprintf('%s Connected', $label))
                ->body(sprintf('PERSCOM is now connected to %s. Choose a channel for notifications.', $name))
                ->send();
        } catch (IntegrationConnectionException $integrationConnectionException) {
            $this->notifyFailure($label, $integrationConnectionException->getMessage());
        } catch (ConnectionException) {
            $this->notifyFailure($label, sprintf('%s couldn\'t be reached. Try again in a minute.', $label));
        }

        return $back;
    }

    protected function notifyFailure(string $label, string $body): void
    {
        Notification::make()
            ->danger()
            ->title(sprintf('%s Not Connected', $label))
            ->body($body)
            ->send();
    }
}
