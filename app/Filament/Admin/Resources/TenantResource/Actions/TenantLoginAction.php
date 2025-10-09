<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TenantResource\Actions;

use App\Models\Tenant;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class TenantLoginAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->color('gray');
        $this->visible(fn (Tenant $record) => $record->setup_completed);

        $this->modalHeading('Login to Tenant');
        $this->modalDescription('Login to the tenant using the user below.');
        $this->modalSubmitActionLabel('Login');

        $this->schema([
            Select::make('user')
                ->searchable()
                ->helperText('Select the user to login as.')
                ->options(fn (Tenant $record) => $record->run(fn () => User::query()->orderBy('name')->whereHas('roles', fn (Builder $query) => $query->where('name', Utils::getSuperAdminName()))->get()->pluck('name', 'id')->toArray()))
                ->required(),
        ]);

        $this->action(function (TenantLoginAction $action, Tenant $record, array $data) {
            // @phpstan-ignore-next-line
            $token = tenancy()->impersonate($record, data_get($data, 'user'), $record->url, 'web');

            return redirect()->to($record->route('tenant.impersonation', [
                'token' => $token,
            ]));
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'login';
    }
}
