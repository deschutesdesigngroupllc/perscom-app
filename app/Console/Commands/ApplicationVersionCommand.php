<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class ApplicationVersionCommand extends Command
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

        return static::SUCCESS;
    }

    public function setApplicationVersion(string $version): int
    {
        if (in_array(preg_match("/^(v+)(\d|[1-9]\d*)\.(\d|[1-9]\d*)\.(\d|[1-9]\d*)(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+[0-9A-Za-z-]+)?$/", $version), [0, false], true)) {
            $this->error('The supplied version is not a valid SemVer version.');

            return static::FAILURE;
        }

        $path = App::environmentFilePath();

        $escaped = preg_quote('='.config('app.version'), '/');

        $this->info("Setting the current application version to: $version");

        file_put_contents($path, preg_replace(
            "/^APP_VERSION{$escaped}/m",
            "APP_VERSION={$version}",
            file_get_contents($path)
        ));

        return static::SUCCESS;
    }

    public function getApplicationVersion(): mixed
    {
        return config('app.version');
    }
}
