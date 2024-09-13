<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\RedisMemoryUsageCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class HealthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Health::checks([
            CacheCheck::new()
                ->driver('redis'),
            CpuLoadCheck::new()
                ->failWhenLoadIsHigherInTheLast5Minutes(80.0)
                ->failWhenLoadIsHigherInTheLast15Minutes(50),
            DatabaseCheck::new(),
            HorizonCheck::new()
                ->if(fn () => app()->environment('local')),
            QueueCheck::new()
                ->onQueue(['default', 'central']),
            OptimizedAppCheck::new(),
            RedisCheck::new(),
            RedisMemoryUsageCheck::new()
                ->failWhenAboveMb(1000),
            ScheduleCheck::new(),
            UsedDiskSpaceCheck::new(),
        ]);
    }
}
