<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RankResource\Pages;

use App\Filament\App\Resources\RankResource;
use App\Models\Category;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRanks extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = RankResource::class;

    protected ?string $subheading = 'Ranks provide a vertical organization hierarchy that establishes authority within an organization.';

    public function getPresetViews(): array
    {
        return Category::all()->where('resource', static::$resource::getModel())->mapWithKeys(fn (Category $category): array => [
            $category->id => PresetView::make()
                ->favorite()
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
