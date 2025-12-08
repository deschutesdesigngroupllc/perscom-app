<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UnitResource\Pages;

use App\Filament\App\Resources\UnitResource;
use App\Models\Unit;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUnits extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = UnitResource::class;

    protected ?string $subheading = 'Units provide grouping and sorting of personnel within the organization.';

    /**
     * @return PresetView[]
     */
    public function getPresetViews(): array
    {
        return [
            PresetView::make('visible')
                ->label('Visible')
                ->modifyQueryUsing(fn (Unit|Builder $query) => $query->where('hidden', false))
                ->favorite()
                ->icon('heroicon-o-eye'),
            PresetView::make('hidden')
                ->label('Hidden')
                ->modifyQueryUsing(fn (Unit|Builder $query) => $query->where('hidden', true))
                ->favorite()
                ->icon('heroicon-o-eye-slash'),
        ];
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
}
