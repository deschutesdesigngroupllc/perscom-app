<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Features;

use App\Features\ExportDataFeature;
use Laravel\Pennant\Feature;
use Tests\Feature\Tenant\TenantTestCase;

class ExportDataFeatureTest extends TenantTestCase
{
    public function test_feature_disabled_by_default(): void
    {
        $this->withoutSubscription();

        $this->assertTrue(Feature::inactive(ExportDataFeature::class));
    }

    public function test_feature_enabled_on_trial(): void
    {
        $this->onTrial();

        $this->assertTrue(Feature::active(ExportDataFeature::class));
    }

    public function test_feature_disabled_on_basic_plan(): void
    {
        $this->withSubscription(env('STRIPE_PRODUCT_BASIC_MONTH'));

        $this->assertTrue(Feature::inactive(ExportDataFeature::class));
    }

    public function test_feature_disabled_on_pro_plan(): void
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'));

        $this->assertTrue(Feature::inactive(ExportDataFeature::class));
    }

    public function test_feature_enabled_on_enterprise_plan(): void
    {
        $this->withSubscription(env('STRIPE_PRODUCT_ENTERPRISE_MONTH'));

        $this->assertTrue(Feature::active(ExportDataFeature::class));
    }
}
