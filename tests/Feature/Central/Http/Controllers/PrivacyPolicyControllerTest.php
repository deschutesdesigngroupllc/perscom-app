<?php

namespace Tests\Feature\Central\Http\Controllers;

use Inertia\Testing\AssertableInertia;
use Tests\Feature\Central\CentralTestCase;

class PrivacyPolicyControllerTest extends CentralTestCase
{
    public function test_privacy_policy_page_can_be_reached()
    {
        $this->get(route('web.privacy-policy.index'))
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('PrivacyPolicy');
            })->assertSuccessful();
    }
}
