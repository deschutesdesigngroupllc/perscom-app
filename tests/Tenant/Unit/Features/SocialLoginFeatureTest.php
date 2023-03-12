<?php

namespace Tests\Tenant\Unit\Features;

use App\Features\SocialLoginFeature;
use Laravel\Pennant\Feature;
use Tests\Tenant\TenantTestCase;

class SocialLoginFeatureTest extends TenantTestCase
{
    public function test_feature_disabled_by_default()
    {
        $this->withoutSubscription();

        $this->assertTrue(Feature::inactive(SocialLoginFeature::class));
    }

    public function test_feature_enabled_on_trial()
    {
        $this->onTrial();

        $this->assertTrue(Feature::active(SocialLoginFeature::class));
    }

    public function test_feature_enabled_on_basic_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_BASIC_MONTH'));

        $this->assertTrue(Feature::active(SocialLoginFeature::class));
    }

    public function test_feature_enabled_on_pro_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'));

        $this->assertTrue(Feature::active(SocialLoginFeature::class));
    }

    public function test_feature_enabled_on_enterprise_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_ENTERPRISE_MONTH'));

        $this->assertTrue(Feature::active(SocialLoginFeature::class));
    }
}
