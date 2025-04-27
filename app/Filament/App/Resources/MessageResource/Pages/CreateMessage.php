<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\MessageResource\Pages;

use App\Filament\App\Resources\MessageResource;
use Filament\Forms;
use Filament\Resources\Pages\CreateRecord;

class CreateMessage extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = MessageResource::class;

    public function getSteps(): array
    {
        return [
            Forms\Components\Wizard\Step::make('Message')
                ->completedIcon('heroicon-m-hand-thumb-up')
                ->schema(MessageResource::messageSchema()),
            Forms\Components\Wizard\Step::make('Channels')
                ->completedIcon('heroicon-m-hand-thumb-up')
                ->schema(MessageResource::channelSchema()),
            Forms\Components\Wizard\Step::make('Details')
                ->completedIcon('heroicon-m-hand-thumb-up')
                ->schema(MessageResource::detailsSchema()),
            Forms\Components\Wizard\Step::make('Schedule')
                ->completedIcon('heroicon-m-hand-thumb-up')
                ->schema(MessageResource::scheduleSchema()),
        ];
    }
}
