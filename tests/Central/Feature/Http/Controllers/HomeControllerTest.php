<?php

namespace Tests\Central\Feature\Http\Controllers;

use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use Tests\Central\CentralTestCase;

class HomeControllerTest extends CentralTestCase
{
    public function test_home_page_can_be_reached()
    {
        $this->get('/')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Home');
            })->assertSuccessful();
    }

    public function test_page_not_found()
    {
        $this->get('/'.Str::random('5'))
            ->assertNotFound();
    }
}
