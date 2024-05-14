<?php

namespace Tests\Feature\Tenant\Http\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Database\Factories\ClientFactory;
use Laravel\Passport\Passport;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Feature\Tenant\TenantTestCase;

class SubscribedTest extends TenantTestCase
{
    public function test_no_subscription_causes_redirect_for_admin()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get(route('nova.pages.dashboard.custom', [
                'name' => 'main',
            ]))
            ->assertStatus(302)
            ->assertRedirectToRoute('spark.portal', [
                'type' => 'tenant',
            ]);
    }

    public function test_no_subscription_causes_error_for_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('nova.pages.dashboard.custom', [
                'name' => 'main',
            ]))
            ->assertPaymentRequired();
    }

    public function test_demo_mode_does_not_require_subscription()
    {
        $this->markTestSkipped('TODO: Fix failing test in CI.');

        config()->set('demo.host', $this->domain->host);
        config()->set('demo.tenant_id', $this->tenant->getTenantKey());

        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get(route('nova.pages.dashboard.custom', [
                'name' => 'main',
            ]))
            ->assertSuccessful();
    }

    public function test_no_subscription_causes_api_error()
    {
        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        URL::forceRootUrl(config('app.api_url').'/'.config('app.api_version'));

        $this->withHeader('X-Perscom-Id', $this->tenant->getTenantKey());

        $this->getJson('/me')
            ->assertJson([
                'error' => [
                    'message' => 'A valid subscription is required to make an API request.',
                ],
            ])
            ->assertPaymentRequired();
    }

    public function test_no_subscription_causes_oauth_error()
    {
        $user = User::factory()->create();
        $client = ClientFactory::new()->create(['user_id' => $user->getKey()]);

        $this->expectException(HttpException::class);

        $this->actingAs($user)
            ->get(route('passport.authorizations.authorize', [
                'response_type' => 'code',
                'client_id' => $client->id,
                'state' => 'test',
                'redirect_url' => $client->redirect,
            ]))
            ->assertJson([
                'error' => [
                    'message' => 'A valid subscription is required to use Single Sign-On (SSO).',
                ],
            ])
            ->assertPaymentRequired();
    }
}
