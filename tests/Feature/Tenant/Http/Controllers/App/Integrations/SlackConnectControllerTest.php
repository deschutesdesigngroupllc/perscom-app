<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\App\Integrations;

use App\Actions\Integrations\DisconnectSlackWorkspace;
use App\Models\User;
use App\Services\IntegrationInstallService;
use App\Services\SlackService;
use App\Settings\IntegrationSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Tests\Feature\Tenant\TenantTestCase;

class SlackConnectControllerTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['services.slack.client_id' => 'client', 'services.slack.client_secret' => 'secret']);
    }

    public function test_install_redirects_to_slack_with_the_bot_scopes(): void
    {
        $this->actingAs($this->admin());

        $location = (string) $this->get(route('app.integrations.install', ['provider' => 'slack']))->headers->get('Location');

        $this->assertStringStartsWith('https://slack.com/oauth/v2/authorize', $location);
        $this->assertStringContainsString('scope=chat:write,chat:write.public,channels:read', urldecode($location));
    }

    public function test_connecting_stores_the_workspace_with_an_encrypted_token(): void
    {
        Http::fake([
            'slack.com/api/oauth.v2.access' => Http::response(['ok' => true, 'access_token' => 'xoxb-new', 'team' => ['id' => 'T1', 'name' => '5th SFG']]),
        ]);

        $this->actingAs($this->admin());

        $state = IntegrationInstallService::state('slack');

        $this->withSession(['integrations.slack.nonce' => Session::get('integrations.slack.nonce')])
            ->get(route('app.integrations.slack.connect', ['state' => $state, 'code' => 'abc']))
            ->assertRedirect();

        $settings = resolve(IntegrationSettings::class);
        $settings->refresh();

        $this->assertSame('5th SFG', data_get($settings->slack_settings, 'team_name'));
        $this->assertNotSame('xoxb-new', data_get($settings->slack_settings, 'bot_token'));
        $this->assertSame('xoxb-new', Crypt::decryptString((string) data_get($settings->slack_settings, 'bot_token')));
        $this->assertSame('xoxb-new', SlackService::token());
    }

    public function test_disconnecting_revokes_the_token_and_clears_the_workspace(): void
    {
        $settings = resolve(IntegrationSettings::class);
        $settings->slack_settings = ['team_name' => '5th SFG', 'bot_token' => Crypt::encryptString('xoxb-old'), 'slack_channel' => 'C1'];
        $settings->save();

        Http::fake(['slack.com/api/auth.revoke' => Http::response(['ok' => true, 'revoked' => true])]);

        DisconnectSlackWorkspace::handle();

        Http::assertSent(fn (Request $request): bool => str_contains($request->url(), 'auth.revoke') && $request->hasHeader('Authorization', 'Bearer xoxb-old'));
        $this->assertNull(resolve(IntegrationSettings::class)->slack_settings);
    }

    protected function admin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        return $user;
    }
}
