<?php

namespace Tests\Feature\Central\Http\Controllers\Swagger;

use Inertia\Testing\AssertableInertia;
use Tests\Feature\Central\CentralTestCase;

class HomeControllerTest extends CentralTestCase
{
    public function test_swagger_home_page_can_be_reached()
    {
        $this->get(route('web.api.documentation'))
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('swagger/Home');
            })->assertSuccessful();
    }
}
