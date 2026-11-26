<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\Pages;

use App\Filament\App\Resources\EventResource;
use App\Filament\Concerns\AdvancedTables;
use App\Models\Calendar;
use App\Models\Category;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListEvents extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = EventResource::class;

    protected ?string $subheading = 'Schedule and manage organizational activities and meetings.';

    public function getPresetViews(): array
    {
        if (! class_exists('Archilex\AdvancedTables\Components\PresetView')) {
            return [];
        }

        $calendars = Calendar::all()->mapWithKeys(fn (Calendar $calendar): array => [
            $calendar->id => PresetView::make()
                ->favorite()
                ->icon(Heroicon::OutlinedCalendar)
                ->label($calendar->name)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereBelongsTo($calendar)),
        ])->toArray();

        return array_merge($calendars, Category::all()->where('resource', static::$resource::getModel())->mapWithKeys(fn (Category $category): array => [
            $category->id => PresetView::make()
                ->label($category->name)
                ->icon(Heroicon::OutlinedTag)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereHas(
                    'categories',
                    fn (Builder $query): Builder => $query->whereKey($category->id),
                ))
                ->favorite(),
        ])->toArray());
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'upcoming' => Tab::make('Upcoming')
                ->icon(Heroicon::OutlinedCalendarDays)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where(
                    fn (Builder $query): Builder => $query->where('repeats', true)->orWhere('ends', '>=', now()),
                )),
            'past' => Tab::make('Past')
                ->icon(Heroicon::OutlinedClock)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('repeats', false)->where('ends', '<', now())),
            'all' => Tab::make('All'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'upcoming';
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
