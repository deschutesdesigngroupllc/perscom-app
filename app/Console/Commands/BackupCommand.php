<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\App;
use Spatie\Backup\Commands\BackupCommand as BaseBackupCommand;
use Spatie\Backup\Config\Config;

class BackupCommand extends BaseBackupCommand
{
    /**
     * @throws BindingResolutionException
     */
    public function handle(): int
    {
        /**
         * We change the database connection to use for the backup in the job
         * so rebind the Config instance so the base backup command has access
         * to the must up-to-date config values.
         */
        Config::rebind();

        $this->config = App::make(Config::class);

        return parent::handle();
    }
}
