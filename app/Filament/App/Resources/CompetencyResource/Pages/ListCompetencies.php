<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CompetencyResource\Pages;

use App\Filament\App\Resources\CompetencyResource;
use App\Models\Category;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCompetencies extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = CompetencyResource::class;

    protected ?string $subheading = 'Specific skills, knowledge, and abilities an individual has acquired and demonstrated through training to perform their job effectively.';

    public function getPresetViews(): array
    {
        return Category::all()->where('resource', static::$resource::getModel())->mapWithKeys(fn (Category $category): array => [
            $category->id => PresetView::make()
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
