<?php

namespace App\Support\Stancl;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Stancl\JobPipeline\JobPipeline as StanclJobPipeline;

class JobPipeline extends StanclJobPipeline implements ShouldHandleEventsAfterCommit
{
}
