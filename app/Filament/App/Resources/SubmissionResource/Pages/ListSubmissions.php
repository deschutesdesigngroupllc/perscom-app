<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SubmissionResource\Pages;

use App\Filament\App\Resources\SubmissionResource;
use App\Models\Submission;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSubmissions extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = SubmissionResource::class;

    protected ?string $subheading = 'View form submissions and collect data.';

    public function getPresetViews(): array
    {
        $count = Submission::query()
            ->unread()
            ->count();

        return [
            PresetView::make('read')
                ->label('Read')
                ->modifyQueryUsing(fn (Submission|Builder $query) => $query->read())
                ->favorite()
                ->icon('heroicon-o-envelope'),
            PresetView::make('unread')
                ->default()
                ->label('Unread')
                ->modifyQueryUsing(fn (Submission|Builder $query) => $query->unread())
                ->badge(fn (): ?string => $count > 0 ? (string) $count : null)
                ->favorite()
                ->icon('heroicon-o-envelope-open'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
