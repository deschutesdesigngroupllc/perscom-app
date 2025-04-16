<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PassportTokenResource\Pages;

use App\Filament\App\Resources\ApiLogResource;
use App\Filament\App\Resources\PassportTokenResource;
use App\Jobs\PurgeApiCache;
use App\Services\ApiCacheService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListPassportTokens extends ListRecords
{
    protected static string $resource = PassportTokenResource::class;

    protected ?string $subheading = 'Manage your application\'s API keys.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('purge')
                ->label('Purge API cache')
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription(new HtmlString("<div class='fi-modal-description text-sm text-gray-500 dark:text-gray-400 flex flex-col space-y-2'><div>PERSCOM leverages a sophisticated caching system to deliver your content globally through our API. Whenever an update occurs, we automatically purge the corresponding item from the cache, ensuring you always access the freshest data. Occasionally, data may become out of sync. If you encounter stale or outdated information through our API, simply purge your cache to retrieve the latest updates.</div><div class='font-semibold'>Please note, it may take a few minutes to propagate the request.</div></div>"))
                ->successNotificationTitle('The API cache has been successfully purged.')
                ->modalSubmitActionLabel('Purge cache')
                ->action(function (Actions\Action $action): void {
                    $apiService = new ApiCacheService;

                    PurgeApiCache::dispatch(
                        tags: $apiService->getTenantCacheTag(),
                        event: 'manual'
                    );

                    $action->success();
                }),
            Actions\Action::make('logs')
                ->label('View logs')
                ->color('gray')
                ->url(fn (): string => ApiLogResource::getUrl()),
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PassportTokenResource\Widgets\ApiRequests::class,
        ];
    }
}
