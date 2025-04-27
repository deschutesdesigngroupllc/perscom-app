<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TenantResource\Pages;

use App\Filament\Admin\Actions\TenantLoginAction;
use App\Filament\Admin\Resources\TenantResource;
use App\Models\Tenant;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            TenantLoginAction::make(),
            Actions\Action::make('stripe')
                ->color(Color::hex('#5167FC'))
                ->visible(fn (Tenant $record) => $record->hasStripeId())
                ->openUrlInNewTab()
                ->url(fn (Tenant $record) => $record->stripe_url),
            Actions\DeleteAction::make(),
        ];
    }
}
