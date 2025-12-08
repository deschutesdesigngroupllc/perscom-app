<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\Pages;

use App\Filament\App\Resources\EventResource;
use App\Models\Calendar;
use App\Models\Category;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListEvents extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = EventResource::class;

    protected ?string $subheading = 'Manage your organizational events and schedules.';

    public function getPresetViews(): array
    {
        $calendars = Calendar::all()->mapWithKeys(fn (Calendar $calendar): array => [
            $calendar->id => PresetView::make()
                ->favorite()
                ->icon('heroicon-o-calendar')
                ->label($calendar->name)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBelongsTo($calendar)),
        ])->toArray();

        return array_merge($calendars, Category::all()->where('resource', static::$resource::getModel())->mapWithKeys(fn (Category $category) => [
            $category->id => PresetView::make()
                ->label($category->name)
                ->icon('heroicon-o-tag')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('categories', fn (Builder $query) => $query->whereKey($category->id)))
                ->favorite(),
        ])->toArray());
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
