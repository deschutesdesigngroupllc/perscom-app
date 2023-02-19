<?php

namespace Tests\Feature\Http\Controllers;

use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class PrivacyPolicyControllerTest extends TestCase
{
    public function test_privacy_policy_page_can_be_reached()
    {
        $this->get('/privacy-policy')
             ->assertInertia(function (AssertableInertia $page) {
                 $page->component('PrivacyPolicy');
             })->assertSuccessful();
    }
}
