<?php

namespace Tests\Traits;

use App\Http\Middleware\LogApiRequests;
use App\Http\Middleware\SentryContext;
use App\Http\Middleware\Subscribed;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Treblle\Middlewares\TreblleMiddleware;

trait MakesApiRequests
{
    public function withoutApiMiddleware()
    {
        $this->withoutMiddleware([
            TreblleMiddleware::class,
            SentryContext::class,
            LogApiRequests::class,
            ThrottleRequests::class,
            Subscribed::class,
        ]);
    }
}
