<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Filament\App\Resources\AssignmentRecordResource;
use App\Filament\App\Resources\AwardRecordResource;
use App\Filament\App\Resources\CombatRecordResource;
use App\Filament\App\Resources\QualificationRecordResource;
use App\Filament\App\Resources\RankRecordResource;
use App\Filament\App\Resources\ServiceRecordResource;
use App\Filament\App\Resources\TrainingRecordResource;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ServiceRecords extends Page
{
    /**
     * @var array<int, class-string<resource>>
     */
    public const array RECORD_RESOURCES = [
        RankRecordResource::class,
        AwardRecordResource::class,
        AssignmentRecordResource::class,
        QualificationRecordResource::class,
        TrainingRecordResource::class,
        CombatRecordResource::class,
        ServiceRecordResource::class,
    ];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Records';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Records';

    /**
     * "service-records" already belongs to the Service Record resource (one of the record types), so the
     * overview lives at /records.
     */
    protected static ?string $slug = 'records';

    protected ?string $subheading = 'Every promotion, award, assignment, qualification, training, combat and service record on file. To add one for a member, use Add Record on their profile.';

    protected string $view = 'filament.app.pages.service-records';

    public static function canAccess(): bool
    {
        return collect(self::RECORD_RESOURCES)->contains(fn (string $resource): bool => $resource::canViewAny());
    }

    /**
     * Get the record types the user can view, with their counts.
     *
     * @return array<int, array{label: string, url: string, icon: mixed, total: int, recent: int}>
     */
    public function getRecordTypes(): array
    {
        return collect(self::RECORD_RESOURCES)
            ->filter(fn (string $resource): bool => $resource::canViewAny())
            ->map(fn (string $resource): array => [
                'label' => $resource::getTitleCasePluralModelLabel(),
                'url' => $resource::getUrl('index'),
                'icon' => $resource::getNavigationIcon(),
                'total' => $resource::getModel()::query()->count(),
                'recent' => $resource::getModel()::query()->where('created_at', '>=', now()->subDays(30))->count(),
            ])
            ->values()
            ->all();
    }
}
