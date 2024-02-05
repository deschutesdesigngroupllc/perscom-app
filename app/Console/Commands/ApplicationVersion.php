<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ApplicationVersion extends Command
{
    protected $signature = 'perscom:version
                            {--set= : Set the current application version}';

    protected $description = 'Retrieves or sets the current application version.';

    public function handle(): int
    {
        $currentApplicationVersion = $this->getApplicationVersion();

        $this->info("The current application version is: $currentApplicationVersion");

        if ($newVersion = $this->option('set')) {
            return $this->setApplicationVersion($newVersion);
        }

        return CommandAlias::SUCCESS;
    }

    public function setApplicationVersion(string $version): int
    {
        if (! preg_match("/^(v+)(\d|[1-9]\d*)\.(\d|[1-9]\d*)\.(\d|[1-9]\d*)(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+[0-9A-Za-z-]+)?$/", $version)) {
            $this->error('The supplied version is not a valid SemVer version.');

            return CommandAlias::FAILURE;
        }

        $path = App::environmentFilePath();

        $escaped = preg_quote('='.env('APP_VERSION'), '/');

        $this->info("Setting the current application version to: $version");

        file_put_contents($path, preg_replace(
            "/^APP_VERSION{$escaped}/m",
            "APP_VERSION={$version}",
            file_get_contents($path)
        ));

        return CommandAlias::SUCCESS;
    }

    public function getApplicationVersion(): mixed
    {
        return env('APP_VERSION');
    }
}
