<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class ApplicationVersionTest extends TestCase
{
    public function test_command_will_fail_return_version()
    {
        $this->artisan('perscom:version')->assertSuccessful();
    }

    public function test_command_updates_environment_file()
    {
        $version = "v{$this->faker->semver()}";

        $this->artisan('perscom:version', [
            '--set' => $version,
        ])->assertSuccessful();

        $envVars = Collection::make(explode("\n", file_get_contents(App::environmentFilePath())))
            ->map(function ($line) {
                return explode('=', $line, 2);
            })
            ->filter(function ($parts) {
                return count($parts) === 2 && trim($parts[0]) !== '';
            })
            ->mapWithKeys(function ($parts) {
                return [trim($parts[0]) => trim($parts[1])];
            });

        $this->assertSame($version, $envVars->get('APP_VERSION'));

        $this->artisan('perscom:version', [
            '--set' => 'v1.0.0',
        ]);
    }

    public function test_command_will_fail_when_not_prefixed_with_v()
    {
        $this->artisan('perscom:version', [
            '--set' => $this->faker->semver(),
        ])->assertFailed();
    }
}
