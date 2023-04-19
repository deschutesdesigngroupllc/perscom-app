<?php

namespace Tests\Central\Feature\Http\Controllers;

use Inertia\Testing\AssertableInertia;
use Tests\Central\CentralTestCase;

class PrivacyPolicyControllerTest extends CentralTestCase
{
    public function test_privacy_policy_page_can_be_reached()
    {
        $this->get('/privacy-policy')
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('PrivacyPolicy');
            })->assertSuccessful();
    }
}
