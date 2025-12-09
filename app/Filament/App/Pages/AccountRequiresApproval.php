<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Tenant;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Pages\SimplePage;
use Illuminate\Support\Facades\Auth;

class AccountRequiresApproval extends SimplePage
{
    protected bool $hasTopbar = false;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.app.pages.account-requires-approval';

    public function mount(): void
    {
        /** @var ?User $user */
        $user = Auth::user();

        /** @var ?Tenant $tenant */
        $tenant = tenant();

        if (blank($user) || blank($tenant)) {
            return;
        }

        if ($user->approved) {
            $this->redirect(Dashboard::getUrl([
                'tenant' => $tenant,
            ]));
        }
    }

    public function logoutAction(): Action
    {
        return Action::make('logout')
            ->color('info')
            ->submit('logout')
            ->link()
            ->action(fn () => $this->redirect('filament.app.login'));
    }
}
