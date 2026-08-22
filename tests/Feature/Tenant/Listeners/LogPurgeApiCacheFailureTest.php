<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Listeners;

use App\Events\ApiPurgedFailed;
use App\Listeners\LogPurgeApiCacheFailure;
use App\Models\Activity;
use Tests\Feature\Tenant\TenantTestCase;

class LogPurgeApiCacheFailureTest extends TenantTestCase
{
    public function test_it_logs_a_failed_api_purge_activity(): void
    {
        $event = new ApiPurgedFailed(['awards'], 'deleted');

        new LogPurgeApiCacheFailure()->handle($event);

        $activity = Activity::query()->where('log_name', 'api_purge')->latest('id')->firstOrFail();

        $this->assertSame('deleted', $activity->description);
        $this->assertSame('failure', $activity->properties->get('status'));
        $this->assertSame(['awards'], $activity->properties->get('tags'));
    }
}
