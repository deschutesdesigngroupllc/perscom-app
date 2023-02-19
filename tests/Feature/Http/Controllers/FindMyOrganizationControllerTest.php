<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Tenant;
use App\Repositories\TenantRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class FindMyOrganizationControllerTest extends TestCase
{
    use WithFaker;

    public function test_find_my_organization_index_page_can_be_reached()
    {
        $this->get('/find-my-organization')
             ->assertInertia(function (AssertableInertia $page) {
                 $page->component('auth/FindMyOrganization');
             })->assertSuccessful();
    }

    public function test_find_my_organization_store_page_can_be_reached()
    {
        $email = $this->faker->email;

        $tenant = $this->mock(Tenant::class);
        $tenant->allows('getAttribute')->with('id')->andReturn(1);

        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->allows('findByKey')->with('email', $email)->andReturn($tenant);

        $this->instance(TenantRepository::class, $tenantRepository);

        $validator = $this->mock(\Illuminate\Validation\Validator::class);
        $validator->allows('validate')->andReturnSelf();
        Validator::shouldReceive('make')->once()->andReturn($validator);

        $url = URL::signedRoute('web.find-my-organization.show', ['tenant' => 1]);

        $response = $this->post('/find-my-organization', [
            'email' => $email,
        ])->assertRedirect($url);
    }

    public function test_find_my_organization_store_page_cannot_be_reached_without_email_parameter()
    {
        $this->post('/find-my-organization')->assertSessionHasErrors('email');
    }

    public function test_find_my_organization_store_page_cannot_be_reached_with_invalid_email_parameter()
    {
        $this->post('/find-my-organization', [
            'email' => $this->faker->word,
        ])->assertSessionHasErrors('email');
    }

    public function test_find_my_organization_store_page_cannot_be_reached_with_nonexistent_email_parameter()
    {
        $email = $this->faker->email;

        $tenant = $this->mock(Tenant::class);
        $tenant->allows('getAttribute')->with('id')->andReturn(1);

        $tenantRepository = $this->mock(TenantRepository::class);
        $tenantRepository->allows('findByKey')->with('email', $email)->andThrow(ModelNotFoundException::class);

        $this->instance(TenantRepository::class, $tenantRepository);

        $validator = $this->mock(\Illuminate\Validation\Validator::class);
        $validator->allows('validate')->andReturnSelf();
        Validator::shouldReceive('make')->once()->andReturn($validator);

        $response = $this->post('/find-my-organization', [
            'email' => $email,
        ])->assertNotFound();
    }

    public function test_find_my_organization_show_page_can_be_reached()
    {
        $tenant = $this->mock(Tenant::class);
        $tenant->allows('resolveRouteBinding')->andReturnSelf();
        $tenant->allows('getAttribute')->with('name')->andReturn('test');
        $tenant->allows('getAttribute')->with('url')->andReturn('test');

        $this->instance(Tenant::class, $tenant);

        $url = URL::signedRoute('web.find-my-organization.show', ['tenant' => 1]);

        $this->get($url)
             ->assertInertia(function (AssertableInertia $page) {
                 $page->component('auth/FindMyOrganization');
             })->assertSuccessful();
    }

    public function test_find_my_organization_show_page_cannot_be_reached_with_invalid_url()
    {
        $tenant = $this->mock(Tenant::class);
        $tenant->allows('resolveRouteBinding')->andReturnSelf();
        $tenant->allows('getAttribute')->with('name')->andReturn('test');
        $tenant->allows('getAttribute')->with('url')->andReturn('test');

        $this->instance(Tenant::class, $tenant);

        $url = URL::signedRoute('web.find-my-organization.show', ['tenant' => 1]);

        $this->get($url)
             ->assertInertia(function (AssertableInertia $page) {
                 $page->component('auth/FindMyOrganization');
             })->assertSuccessful();
    }
}
