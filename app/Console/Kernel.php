<?php

namespace App\Console;

use App\Jobs\RemoveInactiveAccounts;
use App\Jobs\ResetDemoAccount;
use Database\Seeders\MilitarySeeder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('telescope:prune --hours=96')->dailyAt('03:00');
        $schedule->command('queue:prune-failed --hours=96')->dailyAt('04:00');
        $schedule->command('perscom:heartbeat')->environments(['staging', 'production'])->everyTenMinutes();
        $schedule->command('horizon:snapshot')->environments(['staging', 'production'])->everyFiveMinutes();
        $schedule->command('cache:prune-stale-tags')->environments(['staging', 'production'])->hourly();
        $schedule->command('perscom:prune --force --days=7')->environments(['staging', 'production'])->daily();

        $schedule->job(new ResetDemoAccount(MilitarySeeder::class))->environments(['production'])->dailyAt('01:00');
        $schedule->job(new RemoveInactiveAccounts())->environments(['production'])->dailyAt('02:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
