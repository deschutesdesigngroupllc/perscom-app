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

    public function test_page_not_found()
    {
        $this->get('/'.Str::random('5'))
             ->assertStatus(404);
    }
}
