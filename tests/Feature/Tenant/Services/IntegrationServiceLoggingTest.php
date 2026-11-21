<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Services\DiscordService;
use App\Services\SlackService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\Feature\Tenant\TenantTestCase;

class IntegrationServiceLoggingTest extends TenantTestCase
{
    public function test_a_failed_discord_install_is_logged_as_a_warning_with_the_redirect_uri(): void
    {
        Http::fake(['discord.com/api/oauth2/token' => Http::response([
            'error' => 'invalid_grant',
            'error_description' => 'Invalid "redirect_uri" in request.',
        ], 400)]);
        $log = Log::spy();

        $this->assertNull(DiscordService::exchangeInstallCode('code'));

        $log->shouldHaveReceived('warning')->once()->withArgs(fn (string $message, array $context): bool => $message === 'Discord install code exchange failed'
            && $context['error'] === 'invalid_grant'
            && $context['redirect_uri'] === DiscordService::installRedirectUri());
    }

    public function test_a_failed_slack_install_is_logged_as_a_warning_with_the_redirect_uri(): void
    {
        Http::fake(['slack.com/api/oauth.v2.access' => Http::response(['ok' => false, 'error' => 'bad_redirect_uri'])]);
        $log = Log::spy();

        $this->assertNull(SlackService::exchangeInstallCode('code'));

        $log->shouldHaveReceived('warning')->once()->withArgs(fn (string $message, array $context): bool => $message === 'Slack install code exchange failed'
            && $context['error'] === 'bad_redirect_uri'
            && $context['redirect_uri'] === SlackService::installRedirectUri());
    }

    public function test_discord_retry_after_is_read_as_seconds(): void
    {
        $this->assertSame(1500, DiscordService::retryDelayInMilliseconds(['retry_after' => 1.5]));
    }

    public function test_discord_retry_delay_is_capped(): void
    {
        $this->assertLessThanOrEqual(60_000, DiscordService::retryDelayInMilliseconds(['retry_after' => 3600]));
    }
}
