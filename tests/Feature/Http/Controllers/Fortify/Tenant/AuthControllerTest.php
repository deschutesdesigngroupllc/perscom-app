<?php

namespace Tests\Feature\Http\Controllers\Fortify\Tenant;

use Inertia\Testing\AssertableInertia;
use Tests\TestCase;
use Tests\Traits\WithTenant;

class AuthControllerTest extends TestCase
{
    use WithTenant;

    public function test_login_page_can_be_reached()
    {
        $this->get($this->tenant->url.'/login')
             ->assertInertia(function (AssertableInertia $page) {
                 $page->component('auth/Login');
             })->assertSuccessful();
    }

    public function test_register_page_can_be_reached()
    {
        $this->get($this->tenant->url.'/register')
             ->assertInertia(function (AssertableInertia $page) {
                 $page->component('auth/Register');
             })->assertSuccessful();
    }

    public function test_forgot_password_page_can_be_reached()
    {
        $this->get($this->tenant->url.'/forgot-password')
             ->assertInertia(function (AssertableInertia $page) {
                 $page->component('auth/ForgotPassword');
             })->assertSuccessful();
    }
}
