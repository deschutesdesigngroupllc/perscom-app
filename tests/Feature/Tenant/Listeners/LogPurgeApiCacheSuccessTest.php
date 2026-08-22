<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Listeners;

use App\Events\ApiPurgedSuccessful;
use App\Listeners\LogPurgeApiCacheSuccess;
use App\Models\Activity;
use Tests\Feature\Tenant\TenantTestCase;

class LogPurgeApiCacheSuccessTest extends TenantTestCase
{
    public function test_it_logs_a_successful_api_purge_activity(): void
    {
        $event = new ApiPurgedSuccessful(['ranks', 'users'], 'created');

        new LogPurgeApiCacheSuccess()->handle($event);

        $activity = Activity::query()->where('log_name', 'api_purge')->latest('id')->firstOrFail();

        $this->assertSame('created', $activity->description);
        $this->assertSame('success', $activity->properties->get('status'));
        $this->assertSame(['ranks', 'users'], $activity->properties->get('tags'));
    }
}
