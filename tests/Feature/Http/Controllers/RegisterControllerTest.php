<?php

namespace Tests\Feature\Http\Controllers;

use App\Actions\Fortify\CreateNewTenant;
use App\Models\Tenant;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    public function test_register_index_page_can_be_reached()
    {
        $this->get('/register')
             ->assertInertia(function (AssertableInertia $page) {
                 $page->component('Register');
             })->assertSuccessful();
    }

    public function test_register_store_page_can_be_reached()
    {
        $tenant = $this->mock(Tenant::class);
        $tenant->allows('getAttribute')->with('id')->andReturn(1);

        $createNewTenant = $this->mock(CreateNewTenant::class);
        $createNewTenant->allows('create')->with([])->andReturn($tenant);

        $this->instance(CreateNewTenant::class, $createNewTenant);

        $url = URL::signedRoute('web.register.complete', ['tenant' => 1]);

        $this->post('/register')
             ->assertRedirect($url);
    }

    public function test_register_store_page_cannot_be_reached_without_email_parameter()
    {
        $createNewTenant = $this->mock(CreateNewTenant::class);
        $createNewTenant->allows('create')->with([])->andThrow(ValidationException::withMessages(['email' => 'Email is required']));

        $this->instance(CreateNewTenant::class, $createNewTenant);

        $this->post('/register')
             ->assertRedirect()
             ->assertSessionHasErrors('email');
    }

    public function test_register_store_page_cannot_be_reached_without_organization_parameter()
    {
        $createNewTenant = $this->mock(CreateNewTenant::class);
        $createNewTenant->allows('create')->with([])->andThrow(ValidationException::withMessages(['organization' => 'Organization is required']));

        $this->instance(CreateNewTenant::class, $createNewTenant);

        $this->post('/register')
            ->assertRedirect()
            ->assertSessionHasErrors('organization');
    }

    public function test_register_store_page_cannot_be_reached_without_privacy_policy_parameter()
    {
        $createNewTenant = $this->mock(CreateNewTenant::class);
        $createNewTenant->allows('create')->with([])->andThrow(ValidationException::withMessages(['privacy' => 'Privacy policy is required']));

        $this->instance(CreateNewTenant::class, $createNewTenant);

        $this->post('/register')
             ->assertRedirect()
             ->assertSessionHasErrors('privacy');
    }

    public function test_register_complete_page_can_be_reached()
    {
        $tenant = $this->mock(Tenant::class);
        $tenant->allows('resolveRouteBinding')->andReturnSelf();
        $tenant->allows('getAttribute')->with('url')->andReturn('test');

        $this->instance(Tenant::class, $tenant);

        $url = URL::signedRoute('web.register.complete', ['tenant' => 1]);

        $this->get($url)
             ->assertInertia(function (AssertableInertia $page) {
                 $page->component('Complete');
             })->assertSuccessful();
    }
}
