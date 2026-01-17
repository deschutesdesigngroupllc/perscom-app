<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\MessageResource\Pages;

use App\Filament\App\Resources\AutomationResource;
use App\Filament\App\Resources\MessageResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\HtmlString;

class CreateMessage extends CreateRecord
{
    use HasWizard;

    protected static string $resource = MessageResource::class;

    /**
     * @return Step[]
     */
    public function getSteps(): array
    {
        return [
            Step::make('Message')
                ->completedIcon('heroicon-m-hand-thumb-up')
                ->schema([
                    Text::make('tip')
                        ->columnSpanFull()
                        ->content(fn (): HtmlString => new HtmlString(
                            '<div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">'.
                            '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-warning shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" /></svg>'.
                            '<span><strong>Did you know?</strong> You can send this message automatically using <a href="'.AutomationResource::getUrl('create').'" class="font-medium text-primary-600 hover:underline dark:text-primary-400">Automations</a>.</span>'.
                            '</div>'
                        )),
                    ...MessageResource::messageSchema(),
                ]),
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
