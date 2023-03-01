<?php

namespace Tests\Tenant\Feature\Http\Controllers\Passport;

use App\Http\Middleware\Subscribed;
use App\Models\PassportClient;
use Inertia\Testing\AssertableInertia;
use Spatie\Url\Url;
use Tests\Tenant\TenantTestCase;

class AuthorizationControllerTest extends TenantTestCase
{
    public function test_authorize_page_can_be_reached()
    {
        $this->withoutMiddleware(Subscribed::class);

        $client = PassportClient::newFactory()->create();

        $url = Url::fromString($this->tenant->url.'/oauth/authorize')->withQueryParameters([
            'response_type' => 'code',
            'client_id' => $client->id,
            'state' => 'test',
            'redirect_url' => $client->redirect,
        ])->__toString();

        $this->actingAs($this->user)
             ->get($url)
             ->assertInertia(function (AssertableInertia $page) {
                 $page->component('passport/Authorize');
             })->assertSuccessful();
    }
}
