<?php

namespace Tests\Feature\Tenant\Features;

use App\Features\ExportDataFeature;
use Laravel\Pennant\Feature;
use Tests\Feature\Tenant\TenantTestCase;

class ExportDataFeatureTest extends TenantTestCase
{
    public function test_feature_disabled_by_default()
    {
        $this->withoutSubscription();

        ExportDataFeature::resetTenant();

        $this->assertTrue(Feature::inactive(ExportDataFeature::class));
    }

    public function test_feature_enabled_on_trial()
    {
        $this->onTrial();

        ExportDataFeature::resetTenant();

        $this->assertTrue(Feature::active(ExportDataFeature::class));
    }

    public function test_feature_disabled_on_basic_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_BASIC_MONTH'));

        ExportDataFeature::resetTenant();

        $this->assertTrue(Feature::inactive(ExportDataFeature::class));
    }

    public function test_feature_disabled_on_pro_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'));

        ExportDataFeature::resetTenant();

        $this->assertTrue(Feature::inactive(ExportDataFeature::class));
    }

    public function test_feature_enabled_on_enterprise_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_ENTERPRISE_MONTH'));

        ExportDataFeature::resetTenant();

        $this->assertTrue(Feature::active(ExportDataFeature::class));
    }
}
