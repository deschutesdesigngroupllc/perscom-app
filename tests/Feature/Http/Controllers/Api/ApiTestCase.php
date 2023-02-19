<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Middleware\InitializeTenancyByRequestData;
use App\Http\Middleware\LogApiRequests;
use App\Http\Middleware\SentryContext;
use App\Models\Enums\FeatureIdentifier;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\URL;
use Laravel\Cashier\Subscription;
use Orion\Http\Controllers\Controller;
use Spark\Plan;
use Tests\TestCase;

class ApiTestCase extends TestCase
{
    /**
     * @var (Tenant&\Mockery\MockInterface)|(Tenant&\Mockery\MockInterface&\Mockery\MockInterface)|\Mockery\MockInterface
     */
    protected $tenant;

    /**
     * @var (User&\Mockery\MockInterface)|(User&\Mockery\MockInterface&\Mockery\MockInterface)|\Mockery\MockInterface
     */
    protected $user;

    /**
     * @var \Mockery\MockInterface|(Plan&\Mockery\MockInterface)|(Plan&\Mockery\MockInterface&\Mockery\MockInterface)
     */
    protected $plan;

    /**
     * @var (Subscription&\Mockery\MockInterface)|(Subscription&\Mockery\MockInterface&\Mockery\MockInterface)|\Mockery\MockInterface
     */
    protected $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscription = $this->mock(Subscription::class);

        $this->plan = $this->mock(Plan::class);
        $this->plan->allows('options')->passthru()->andReturnSelf();
        $this->plan->options([
            FeatureIdentifier::FEATURE_API_ACCESS,
        ]);

        $this->tenant = $this->mock(Tenant::class);
        $this->tenant->allows('subscription')->andReturn($this->subscription);
        $this->tenant->allows('sparkPlan')->andReturn($this->plan);

        $this->instance(\Stancl\Tenancy\Contracts\Tenant::class, $this->tenant);

        $this->user = User::factory()->make();

        URL::forceRootUrl(config('app.api_url').'/'.config('app.api_version'));

        $this->withoutMiddleware([
            InitializeTenancyByRequestData::class,
            'treblle',
            SentryContext::class,
            LogApiRequests::class,
            ThrottleRequests::class,
            'subscribed',
        ]);

        $apiController = $this->mock(Controller::class);
        $apiController->allows('index')->andReturn(new JsonResponse());

        $this->instance(Controller::class, $apiController);
    }
}
