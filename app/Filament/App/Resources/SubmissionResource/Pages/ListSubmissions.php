<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SubmissionResource\Pages;

use App\Filament\App\Resources\SubmissionResource;
use App\Filament\Concerns\AdvancedTables;
use App\Models\Form;
use App\Models\Submission;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSubmissions extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = SubmissionResource::class;

    protected ?string $subheading = 'Review and process form responses.';

    public function getPresetViews(): array
    {
        if (! class_exists('Archilex\AdvancedTables\Components\PresetView')) {
            return [];
        }

        $count = Submission::query()
            ->unread()
            ->count();

        $views = [
            \Archilex\AdvancedTables\Components\PresetView::make('read')
                ->label('Read')
                ->modifyQueryUsing(fn (Submission|Builder $query) => $query->read())
                ->favorite()
                ->icon('heroicon-o-envelope'),
            \Archilex\AdvancedTables\Components\PresetView::make('unread')
                ->default()
                ->label('Unread')
                ->modifyQueryUsing(fn (Submission|Builder $query) => $query->unread())
                ->badge(fn (): ?string => $count > 0 ? (string) $count : null)
                ->favorite()
                ->icon('heroicon-o-envelope-open'),
        ];

        return array_merge($views, Form::all()->mapWithKeys(fn (Form $form): array => [
            $form->id => \Archilex\AdvancedTables\Components\PresetView::make()
                ->label($form->name)
                ->modifyQueryUsing(fn (Submission|Builder $query) => $query->whereBelongsTo($form))
                ->favorite()
                ->icon('heroicon-o-pencil-square'),
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
