<?php

namespace App\Console;

use App\Jobs\ResetDemoAccount;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('telescope:prune --hours=96')->daily();
        $schedule->command('perscom:heartbeat')->environments(['staging', 'production'])->everyTenMinutes();
        $schedule->command('horizon:snapshot')->environments(['staging', 'production'])->everyFiveMinutes();
        $schedule->command('cache:prune-stale-tags')->environments(['staging', 'production'])->hourly();

        $schedule->job(new ResetDemoAccount())->environments(['production'])->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
