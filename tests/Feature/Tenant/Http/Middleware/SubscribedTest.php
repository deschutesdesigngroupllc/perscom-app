<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Middleware;

use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
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
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user)
            ->get(route('filament.app.pages.dashboard', [
                'tenant' => $this->tenant,
            ]))
            ->assertStatus(302)
            ->assertRedirectToRoute('filament.app.tenant.billing', [
                'tenant' => $this->tenant,
            ]);
    }

    public function test_no_subscription_causes_api_error()
    {
        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        URL::forceRootUrl(config('api.url').'/'.config('api.version'));

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
