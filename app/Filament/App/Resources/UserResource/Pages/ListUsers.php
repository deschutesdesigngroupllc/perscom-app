<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use App\Models\Status;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Components\PresetView;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = UserResource::class;

    protected ?string $subheading = 'Manage your organization\'s personnel.';

    public function getPresetViews(): array
    {
        return Status::all()->mapWithKeys(fn (Status $status): array => [
            $status->id => PresetView::make()
                ->favorite()
                ->label($status->name)
                ->color(Color::generateV3Palette($status->color))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status_id', $status->id)),
        ])->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
