<?php

namespace Tests\Central\Feature\Http\Controllers\Swagger;

use Inertia\Testing\AssertableInertia;
use Tests\Central\CentralTestCase;

class HomeControllerTest extends CentralTestCase
{
    public function test_swagger_home_page_can_be_reached()
    {
        $this->get('/documentation/api')
             ->assertInertia(function (AssertableInertia $page) {
                 $page->component('swagger/Home');
             })->assertSuccessful();
    }
}
