<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Batches\CalculateSchedules as CalculateSchedulesAction;
use Illuminate\Console\Command;
use Throwable;

class CalculateSchedules extends Command
{
    protected $signature = 'perscom:calculate-schedules';

    protected $description = 'Calculates the next occurrence of a schedule and updates the database record.';

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        CalculateSchedulesAction::handle();

        $this->info('The schedule job has been dispatched.');

        return static::SUCCESS;
    }
}
