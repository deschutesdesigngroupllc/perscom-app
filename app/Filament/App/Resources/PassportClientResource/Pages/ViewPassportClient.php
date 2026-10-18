<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PassportClientResource\Pages;

use App\Filament\App\Resources\PassportClientResource;
use App\Models\PassportClient;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;

class ViewPassportClient extends ViewRecord
{
    public const string PLAIN_SECRET_SESSION_KEY = 'passport_client_plain_secret';

    #[Locked]
    public ?string $plainSecret = null;

    protected static string $resource = PassportClientResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $secret = session()->pull(static::PLAIN_SECRET_SESSION_KEY);

        if (is_string($secret) && filled($secret)) {
            $this->plainSecret = $secret;
            $this->defaultAction = 'showSecret';
        }
    }

    public function showSecretAction(): Action
    {
        return Action::make('showSecret')
            ->modalHeading('Client Secret')
            ->modalDescription("Copy this secret now and store it somewhere safe. It won't be shown again.")
            ->modalIcon(Heroicon::OutlinedKey)
            ->fillForm(fn (): array => ['secret' => $this->plainSecret])
            ->schema([
                TextInput::make('secret')
                    ->hiddenLabel()
                    ->readOnly()
                    ->copyable(),
            ])
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Done');
    }

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('regenerateSecret')
                ->label('Regenerate Secret')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Regenerate Client Secret')
                ->modalDescription('The current secret stops working immediately. Any app using it will need the new one.')
                ->modalSubmitActionLabel('Regenerate')
                ->action(function (PassportClient $record): void {
                    $secret = Str::random(40);

                    $record->update(['secret' => $secret]);

                    $this->plainSecret = $secret;
                    $this->replaceMountedAction('showSecret');
                }),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
