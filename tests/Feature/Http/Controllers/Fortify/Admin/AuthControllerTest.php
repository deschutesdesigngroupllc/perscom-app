<?php

namespace Tests\Feature\Http\Controllers\Fortify\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_login_page_can_be_reached()
    {
        $this->get('/admin/login')
             ->assertInertia(function (AssertableInertia $page) {
                 $page->component('auth/Login');
             })->assertSuccessful();
    }

    public function test_register_page_cannot_be_reached()
    {
        $this->get('/admin/register')
             ->assertNotFound();
    }

    public function test_forgot_password_page_cannot_be_reached()
    {
        $this->get('/admin/forgot-password')
             ->assertNotFound();
    }
}
