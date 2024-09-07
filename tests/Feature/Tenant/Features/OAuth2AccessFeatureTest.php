<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Features;

use App\Features\OAuth2AccessFeature;
use Laravel\Pennant\Feature;
use Tests\Feature\Tenant\TenantTestCase;

class OAuth2AccessFeatureTest extends TenantTestCase
{
    public function test_feature_disabled_by_default()
    {
        $this->withoutSubscription();

        $this->assertTrue(Feature::inactive(OAuth2AccessFeature::class));
    }

    public function test_feature_enabled_on_trial()
    {
        $this->onTrial();

        $this->assertTrue(Feature::active(OAuth2AccessFeature::class));
    }

    public function test_feature_disabled_on_basic_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_BASIC_MONTH'));

        $this->assertTrue(Feature::inactive(OAuth2AccessFeature::class));
    }

    public function test_feature_disabled_on_pro_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'));

        $this->assertTrue(Feature::inactive(OAuth2AccessFeature::class));
    }

    public function test_feature_enabled_on_enterprise_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_ENTERPRISE_MONTH'));

        $this->assertTrue(Feature::active(OAuth2AccessFeature::class));
    }
}
