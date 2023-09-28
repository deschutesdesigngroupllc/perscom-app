<?php

namespace Tests\Feature\Tenant\Requests\Traits;

use App\Http\Middleware\LogApiRequests;
use App\Http\Middleware\SentryContext;
use App\Http\Middleware\Subscribed;
use Illuminate\Routing\Middleware\ThrottleRequests;

trait MakesApiRequests
{
    public function withoutApiMiddleware()
    {
        $this->withoutMiddleware([
            SentryContext::class,
            LogApiRequests::class,
            ThrottleRequests::class,
            Subscribed::class,
        ]);
    }
}
