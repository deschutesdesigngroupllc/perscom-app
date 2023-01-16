<?php

namespace Tests\Feature\Browser\Inertia;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class PageTest extends TestCase
{
    use WithFaker;

    public function test_home_page_can_be_reached()
    {
        $this->get('/')
             ->assertInertia(function (AssertableInertia $page) {
                $page->component('Home');
            })->assertSuccessful();
    }

    public function test_register_page_can_be_reached()
    {
        $this->get('/register')
             ->assertInertia(function (AssertableInertia $page) {
                $page->component('Register');
            })->assertSuccessful();
    }

    public function test_register_completion_can_be_reached()
    {
        $this->post('/register', [
            'organization' => $this->faker->company(),
            'email'        => $this->faker->companyEmail(),
            'website'      => $this->faker->url(),
            'domain'       => $this->faker->domainWord()
        ])->assertRedirectContains('/register/complete');
    }

    public function test_register_completion_redirect_back_without_tenant_data()
    {
        $this->get('/register/complete/' . random_int(1, 10))
             ->assertRedirectToRoute('register.index')
             ->assertRedirect('/register');
    }

    public function test_page_not_found()
    {
        $this->get('/' . Str::random('5'))
             ->assertStatus(404);
    }
}
