<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FormResource\Pages;

use App\Filament\App\Resources\FormResource;
use App\Models\Category;
use App\Models\Form;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListForms extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = FormResource::class;

    protected ?string $subheading = 'Collect structured data through customizable input forms.';

    /**
     * @return PresetView[]
     */
    public function getPresetViews(): array
    {
        $views = [
            PresetView::make('public')
                ->label('Public')
                ->modifyQueryUsing(fn (Form|Builder $query) => $query->where('is_public', true))
                ->favorite()
                ->icon('heroicon-o-eye'),
            PresetView::make('private')
                ->label('Private')
                ->modifyQueryUsing(fn (Form|Builder $query) => $query->where('is_public', false))
                ->favorite()
                ->icon('heroicon-o-eye-slash'),
        ];

        return array_merge($views, Category::all()->where('resource', static::$resource::getModel())->mapWithKeys(fn (Category $category): array => [
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
