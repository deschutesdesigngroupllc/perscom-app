<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AwardResource\Pages;

use App\Filament\App\Resources\AwardResource;
use App\Filament\Concerns\AdvancedTables;
use App\Models\Category;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAwards extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = AwardResource::class;

    protected ?string $subheading = 'Recognize personnel achievements with decorations, medals, and commendations.';

    public function getPresetViews(): array
    {
        if (! class_exists('Archilex\AdvancedTables\Components\PresetView')) {
            return [];
        }

        return Category::all()->where('resource', static::$resource::getModel())->mapWithKeys(fn (Category $category): array => [
            $category->id => \Archilex\AdvancedTables\Components\PresetView::make()
                ->favorite()
                ->icon('heroicon-o-tag')
                ->label($category->name)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('categories', fn (Builder $query) => $query->whereKey($category->id))),
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
}
