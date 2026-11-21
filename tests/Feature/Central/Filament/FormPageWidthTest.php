<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Filament;

use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Tests\Feature\Central\CentralTestCase;

class FormPageWidthTest extends CentralTestCase
{
    public function test_every_create_and_edit_page_uses_the_standard_form_width(): void
    {
        $pages = collect(Finder::create()->files()->in(app_path('Filament'))->name(['Create*.php', 'Edit*.php']))
            ->map(fn ($file): string => 'App\\'.Str::of($file->getRealPath())->after(app_path().DIRECTORY_SEPARATOR)->beforeLast('.php')->replace(DIRECTORY_SEPARATOR, '\\'))
            ->filter(fn (string $class): bool => class_exists($class) && (is_subclass_of($class, CreateRecord::class) || is_subclass_of($class, EditRecord::class)));

        $this->assertNotEmpty($pages);

        $pages->each(fn (string $class) => $this->assertSame(
            Width::SevenExtraLarge,
            (new ReflectionClass($class))->getDefaultProperties()['maxContentWidth'] ?? null,
            "{$class} should set \$maxContentWidth to Width::SevenExtraLarge.",
        ));
    }
}
