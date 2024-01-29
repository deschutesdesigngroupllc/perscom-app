<?php

namespace Tests\Feature\Central\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use Tests\Feature\Central\CentralTestCase;

class HomeControllerTest extends CentralTestCase
{
    use RefreshDatabase;

    public function test_home_page_can_be_reached()
    {
        $this->get(route('web.landing.home'))
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Home');
            })->assertSuccessful();
    }

    public function test_page_not_found()
    {
        $this->get(route('web.landing.home').'/'.Str::random('5'))
            ->assertNotFound();
    }
}
