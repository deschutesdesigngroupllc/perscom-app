<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class ApplicationVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perscom:version
                            {--set= : Set the current application version}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieves or sets the current application version.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $currentApplicationVersion = $this->getApplicationVersion();

        $this->info("The current application version is: $currentApplicationVersion");

        if ($newVersion = $this->option('set')) {
            $this->setApplicationVersion($newVersion);
        }

        return Command::SUCCESS;
    }

    /**
     * @return int
     */
    public function setApplicationVersion($version)
    {
        if (! preg_match("/^(v+)(\d|[1-9]\d*)\.(\d|[1-9]\d*)\.(\d|[1-9]\d*)(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+[0-9A-Za-z-]+)?$/", $version)) {
            $this->error('The supplied version is not a valid SemVer version.');

            return Command::FAILURE;
        }

        $path = App::environmentFilePath();

        $escaped = preg_quote('='.env('APP_VERSION'), '/');

        $this->info("Setting the current application version to: $version");

        file_put_contents($path, preg_replace(
            "/^APP_VERSION{$escaped}/m",
            "APP_VERSION={$version}",
            file_get_contents($path)
        ));

        return Command::SUCCESS;
    }

    /**
     * @return mixed
     */
    public function getApplicationVersion()
    {
        return env('APP_VERSION');
    }
}
