<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RankRecordResource\Pages;

use App\Filament\App\Resources\RankRecordResource;
use App\Models\Enums\RankRecordType;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ListRankRecords extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = RankRecordResource::class;

    protected ?string $subheading = 'Keep track of promotions, demotions and lateral transfers throughout the organization.';

    public function getPresetViews(): array
    {
        return Collection::wrap(RankRecordType::cases())->mapWithKeys(fn (RankRecordType $type) => [
            $type->value => PresetView::make()
                ->favorite()
                ->icon($type->getIcon())
                ->label($type->getLabel())
                ->color($type->getColor())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', $type->value)),
        ])->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RankRecordResource\Widgets\RankRecordStatsOverview::class,
        ];
    }
}
