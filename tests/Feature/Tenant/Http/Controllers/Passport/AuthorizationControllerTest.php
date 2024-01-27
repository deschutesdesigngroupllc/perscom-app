<?php

namespace Tests\Feature\Tenant\Http\Controllers\Passport;

use App\Http\Middleware\Subscribed;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Laravel\Passport\Database\Factories\ClientFactory;
use Spatie\Url\Url;
use Tests\Feature\Tenant\TenantTestCase;

class AuthorizationControllerTest extends TenantTestCase
{
    public function test_authorization_page_can_be_reached()
    {
        $this->withoutMiddleware(Subscribed::class);

        $user = User::factory()->create();

        $client = ClientFactory::new()->create(['user_id' => $user->getKey()]);

        $url = Url::fromString($this->tenant->url.'/oauth/authorize')->withQueryParameters([
            'response_type' => 'code',
            'client_id' => $client->id,
            'state' => 'test',
            'redirect_url' => $client->redirect,
        ])->__toString();

        $this->actingAs($user)
            ->get($url)
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('passport/Authorize');
            })->assertSuccessful();
    }
}
