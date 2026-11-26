<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UnitResource\Pages;

use App\Filament\App\Resources\UnitResource;
use App\Filament\Concerns\AdvancedTables;
use App\Models\Unit;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListUnits extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = UnitResource::class;

    protected ?string $subheading = 'Organize personnel into teams, squads, and other operational subdivisions.';

    public function getPresetViews(): array
    {
        if (! class_exists('Archilex\AdvancedTables\Components\PresetView')) {
            return [];
        }

        return [
            PresetView::make('visible')
                ->label('Visible')
                ->modifyQueryUsing(fn (Unit|Builder $query): Builder => $query->where('hidden', false))
                ->favorite()
                ->icon(Heroicon::OutlinedEye),
            PresetView::make('hidden')
                ->label('Hidden')
                ->modifyQueryUsing(fn (Unit|Builder $query): Builder => $query->where('hidden', true))
                ->favorite()
                ->icon(Heroicon::OutlinedEyeSlash),
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
