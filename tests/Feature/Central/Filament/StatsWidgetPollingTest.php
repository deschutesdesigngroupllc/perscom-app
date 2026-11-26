<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Filament;

use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Tests\Feature\Central\CentralTestCase;

class StatsWidgetPollingTest extends CentralTestCase
{
    public function test_app_stats_widgets_do_not_poll(): void
    {
        $widgets = collect(Finder::create()->files()->in(app_path('Filament/App'))->name('*.php'))
            ->map(fn ($file): string => 'App\\'.Str::of($file->getRealPath())->after(app_path().DIRECTORY_SEPARATOR)->beforeLast('.php')->replace(DIRECTORY_SEPARATOR, '\\'))
            ->filter(fn (string $class): bool => class_exists($class) && is_subclass_of($class, StatsOverviewWidget::class));

        $this->assertNotEmpty($widgets);

        $widgets->each(function (string $class): void {
            $defaults = new ReflectionClass($class)->getDefaultProperties();

            $this->assertArrayHasKey('pollingInterval', $defaults);
            $this->assertNull($defaults['pollingInterval'], "{$class} should not poll.");
        });
    }
}
