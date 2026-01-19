<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\GroupResource\Pages;

use App\Filament\App\Resources\GroupResource;
use App\Models\Group;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListGroups extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = GroupResource::class;

    protected ?string $subheading = 'Define major divisions, departments, or branches that contain units.';

    /**
     * @return PresetView[]
     */
    public function getPresetViews(): array
    {
        return [
            PresetView::make('visible')
                ->label('Visible')
                ->modifyQueryUsing(fn (Group|Builder $query) => $query->where('hidden', false))
                ->favorite()
                ->icon('heroicon-o-eye'),
            PresetView::make('hidden')
                ->label('Hidden')
                ->modifyQueryUsing(fn (Group|Builder $query) => $query->where('hidden', true))
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
