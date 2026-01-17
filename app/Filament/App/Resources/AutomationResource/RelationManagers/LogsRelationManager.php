<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AutomationResource\RelationManagers;

use App\Filament\App\Resources\AutomationLogResource;
use App\Models\AutomationLog;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    public function table(Table $table): Table
    {
        return AutomationLogResource::table($table)
            ->searchable(false)
            ->heading('Automation Logs')
            ->emptyStateHeading('No automation logs')
            ->description('The execution logs for this automation.')
            ->emptyStateDescription('There are no execution logs for this automation yet.')
            ->recordActions([
                ViewAction::make()
                    ->url(fn (AutomationLog $record): string => AutomationLogResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
