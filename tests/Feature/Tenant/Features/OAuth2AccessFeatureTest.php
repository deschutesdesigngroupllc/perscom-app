<?php

namespace Tests\Feature\Tenant\Features;

use App\Features\OAuth2AccessFeature;
use Laravel\Pennant\Feature;
use Tests\Feature\Tenant\TenantTestCase;

class OAuth2AccessFeatureTest extends TenantTestCase
{
    public function test_feature_disabled_by_default()
    {
        $this->withoutSubscription();

        OAuth2AccessFeature::resetTenant();

        $this->assertTrue(Feature::inactive(OAuth2AccessFeature::class));
    }

    public function test_feature_enabled_on_trial()
    {
        $this->onTrial();

        OAuth2AccessFeature::resetTenant();

        $this->assertTrue(Feature::active(OAuth2AccessFeature::class));
    }

    public function test_feature_disabled_on_basic_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_BASIC_MONTH'));

        OAuth2AccessFeature::resetTenant();

        $this->assertTrue(Feature::inactive(OAuth2AccessFeature::class));
    }

    public function test_feature_disabled_on_pro_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'));

        OAuth2AccessFeature::resetTenant();

        $this->assertTrue(Feature::inactive(OAuth2AccessFeature::class));
    }

    public function test_feature_enabled_on_enterprise_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_ENTERPRISE_MONTH'));

        OAuth2AccessFeature::resetTenant();

        $this->assertTrue(Feature::active(OAuth2AccessFeature::class));
    }
}
