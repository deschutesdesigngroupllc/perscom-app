<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TenantResource\Pages;

use App\Filament\Admin\Resources\TenantResource;
use App\Models\Tenant;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('login')
                ->color('gray')
                ->modalDescription('Login to the tenant using the user below.')
                ->visible(fn (Tenant $record) => $record->setup_completed)
                ->form([
                    Forms\Components\Select::make('user')
                        ->searchable()
                        ->helperText('Select the user to login as.')
                        ->options(fn (Tenant $record) => $record->run(fn () => User::query()->orderBy('name')->whereHas('roles', function (Builder $query): void {
                            $query->where('name', Utils::getSuperAdminName());
                        })->get()->pluck('name', 'id')->toArray()))
                        ->required(),
                ])
                ->action(function (Actions\Action $action, Tenant $record, array $data) {
                    // @phpstan-ignore-next-line
                    $token = tenancy()->impersonate($record, data_get($data, 'user'), $record->url, 'web');

                    return redirect()->to($record->route('tenant.impersonation', [
                        'token' => $token,
                    ]));
                }),
            Actions\Action::make('stripe')
                ->color(Color::hex('#5167FC'))
                ->visible(fn (Tenant $record) => $record->hasStripeId())
                ->openUrlInNewTab()
                ->url(fn (Tenant $record) => $record->stripe_url),
            Actions\DeleteAction::make(),
        ];
    }
}
