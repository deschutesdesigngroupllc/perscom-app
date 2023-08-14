<?php

namespace Tests\Feature\Central\Rules;

use App\Rules\SubdomainRule;
use Tests\Feature\Central\CentralTestCase;

class SubdomainRuleTest extends CentralTestCase
{
    public function test_protected_subdomains_do_not_pass_test()
    {
        $rule = new SubdomainRule();

        foreach (SubdomainRule::$reservedSubdomains as $domain) {
            $passed = true;

            $fail = function () use (&$passed) {
                $passed = false;
            };

            $rule->validate('domain', $domain, $fail);

            $this->assertFalse($passed);
        }
    }

    public function test_random_subdomain_passes_test()
    {
        $rule = new SubdomainRule();

        $passed = true;

        $fail = function () use (&$passed) {
            $passed = false;
        };

        $rule->validate('domain', $this->faker->domainName, $fail);

        $this->assertTrue($passed);
    }
}
