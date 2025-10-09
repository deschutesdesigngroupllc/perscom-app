<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TenantResource\Actions;

use App\Models\Tenant;
use Exception;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;

class SwapSubscriptionBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Swap Subscription');
        $this->requiresConfirmation();

        $this->modalHeading('Swap Subscription');
        $this->modalDescription('Enter the new price ID to swap the subscription of the tenant.');
        $this->modalSubmitActionLabel('Swap');
        $this->modalIcon('heroicon-o-arrow-path');

        $this->groupedIcon('heroicon-m-arrow-path');

        $this->form([
            TextInput::make('price_id')
                ->required()
                ->label('Price ID'),
            Checkbox::make('charge_immediately')
                ->label('Charge Immediately')
                ->default(true)
                ->helperText('Whether to charge the customer immediately or wait until the next billing cycle.'),
        ]);

        $this->action(function (SwapSubscriptionBulkAction $action, Collection $records, array $data): void {
            try {
                match (data_get($data, 'charge_immediately') ?? false) {
                    true => $records->each(fn (Tenant $record) => $record->subscription()?->swapAndInvoice(data_get($data, 'price_id'))),
                    default => $records->each(fn (Tenant $record) => $record->subscription()?->swap(data_get($data, 'price_id'))),
                };
            } catch (Exception $e) {
                $action->failureNotificationTitle($e->getMessage());
                $action->failure();
            }
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'swap';
    }
}
