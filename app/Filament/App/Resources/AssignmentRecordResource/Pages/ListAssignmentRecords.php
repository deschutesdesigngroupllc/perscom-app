<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AssignmentRecordResource\Pages;

use App\Filament\App\Resources\AssignmentRecordResource;
use App\Filament\App\Resources\AssignmentRecordResource\Widgets\AssignmentRecordStatsOverview;
use App\Models\Enums\AssignmentRecordType;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ListAssignmentRecords extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = AssignmentRecordResource::class;

    protected ?string $subheading = "Keep track of a user's position within the organizational hierarchy with ease.";

    public function getPresetViews(): array
    {
        return Collection::make(AssignmentRecordType::cases())->mapWithKeys(fn (AssignmentRecordType $recordType): array => [
            $recordType->value => PresetView::make()
                ->favorite()
                ->label($recordType->getLabel())
                ->color($recordType->getColor())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', $recordType)),
        ])->toArray();
    }

    /**
     * @return CreateAction[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AssignmentRecordStatsOverview::class,
        ];
    }
}
