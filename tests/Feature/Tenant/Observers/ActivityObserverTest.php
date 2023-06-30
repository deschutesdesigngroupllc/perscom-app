<?php

namespace Tests\Feature\Tenant\Observers;

use App\Features\OpenAiGeneratedContent;
use App\Jobs\GenerateOpenAiNewsfeedContent;
use App\Models\Activity;
use App\Models\ServiceRecord;
use Illuminate\Support\Facades\Queue;
use Laravel\Pennant\Feature;
use Tests\Feature\Tenant\TenantTestCase;

class ActivityObserverTest extends TenantTestCase
{
    public function test_newsfeed_activity_log_create_pushes_openai_job()
    {
        Queue::fake([GenerateOpenAiNewsfeedContent::class]);

        Feature::store('database')->for($this->tenant)->activate(OpenAiGeneratedContent::class);

        Activity::factory()->state([
            'log_name' => 'newsfeed',
        ])->for(ServiceRecord::factory()->createQuietly(), 'subject')->create();

        Queue::assertPushed(GenerateOpenAiNewsfeedContent::class);
    }
}
