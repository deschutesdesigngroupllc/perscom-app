<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\LogApiRequest;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Routing\Middleware\ThrottleRequests;

trait MakesApiRequests
{
    use MakesHttpRequests;

    public function withoutApiMiddleware(): void
    {
        $this->withoutMiddleware([
            LogApiRequest::class,
            ThrottleRequests::class,
            CheckSubscription::class,
        ]);
    }
}
