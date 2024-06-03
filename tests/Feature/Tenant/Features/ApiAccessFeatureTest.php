<?php

namespace Tests\Feature\Tenant\Features;

use App\Features\ApiAccessFeature;
use Laravel\Pennant\Feature;
use Tests\Feature\Tenant\TenantTestCase;

class ApiAccessFeatureTest extends TenantTestCase
{
    public function test_feature_disabled_by_default()
    {
        $this->withoutSubscription();

        ApiAccessFeature::resetTenant();

        $this->assertTrue(Feature::inactive(ApiAccessFeature::class));
    }

    public function test_feature_enabled_on_trial()
    {
        $this->onTrial();

        ApiAccessFeature::resetTenant();

        $this->assertTrue(Feature::active(ApiAccessFeature::class));
    }

    public function test_feature_disabled_on_basic_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_BASIC_MONTH'));

        ApiAccessFeature::resetTenant();

        $this->assertTrue(Feature::inactive(ApiAccessFeature::class));
    }

    public function test_feature_enabled_on_pro_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'));

        ApiAccessFeature::resetTenant();

        $this->assertTrue(Feature::active(ApiAccessFeature::class));
    }

    public function test_feature_enabled_on_enterprise_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_ENTERPRISE_MONTH'));

        ApiAccessFeature::resetTenant();

        $this->assertTrue(Feature::active(ApiAccessFeature::class));
    }
}
