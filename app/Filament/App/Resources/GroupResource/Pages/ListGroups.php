<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\GroupResource\Pages;

use App\Filament\App\Resources\GroupResource;
use App\Filament\Concerns\AdvancedTables;
use App\Models\Group;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListGroups extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = GroupResource::class;

    protected ?string $subheading = 'Define major divisions, departments, or branches that contain units.';

    public function getPresetViews(): array
    {
        if (! class_exists('Archilex\AdvancedTables\Components\PresetView')) {
            return [];
        }

        return [
            \Archilex\AdvancedTables\Components\PresetView::make('visible')
                ->label('Visible')
                ->modifyQueryUsing(fn (Group|Builder $query) => $query->where('hidden', false))
                ->favorite()
                ->icon('heroicon-o-eye'),
            \Archilex\AdvancedTables\Components\PresetView::make('hidden')
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
