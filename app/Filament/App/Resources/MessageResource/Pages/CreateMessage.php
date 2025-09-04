<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\MessageResource\Pages;

use App\Filament\App\Resources\MessageResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Wizard\Step;

class CreateMessage extends CreateRecord
{
    use HasWizard;

    protected static string $resource = MessageResource::class;

    public function getSteps(): array
    {
        return [
            Step::make('Message')
                ->completedIcon('heroicon-m-hand-thumb-up')
                ->schema(MessageResource::messageSchema()),
            Step::make('Channels')
                ->completedIcon('heroicon-m-hand-thumb-up')
                ->schema(MessageResource::channelSchema()),
            Step::make('Details')
                ->completedIcon('heroicon-m-hand-thumb-up')
                ->schema(MessageResource::detailsSchema()),
            Step::make('Schedule')
                ->completedIcon('heroicon-m-hand-thumb-up')
                ->schema(MessageResource::scheduleSchema()),
        ];
    }
}
