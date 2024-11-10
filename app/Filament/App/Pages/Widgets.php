<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Filament\App\Resources\PassportTokenResource;
use App\Forms\Components\TorchlightCode;
use App\Forms\Components\WidgetCodeGenerator;
use App\Models\PassportToken;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class Widgets extends Page
{
    public ?string $apiKey;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    protected static ?string $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.app.pages.widgets';

    protected ?string $subheading = 'Widgets provide a visual representation of your personnel data that can be embedded in any external website. Use the widget explorer below to try the widgets out in real-time.';

    public function mount(): void
    {
        /** @var JWTGuard $guard */
        $guard = Auth::guard('jwt');

        /** @var User $user */
        $user = Auth::guard('web')->user();

        $this->apiKey = $guard->claims([
            'scopes' => '*',
        ])->login($user);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('code')
                ->label('Get Widget Code')
                ->slideOver()
                ->modalDescription('Use the form below to configure and generate the code necessary to embed a widget in your website.')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->formId('widget-generator')
                ->form([
                    Select::make('widget')
                        ->helperText('The widget to display.')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->updateCodeSnippet($get, $set))
                        ->options([
                            'roster' => 'Roster',
                            'awards' => 'Awards',
                            'ranks' => 'Ranks',
                            'qualifications' => 'Qualifications',
                        ]),
                    Select::make('api_key')
                        ->label('API Key')
                        ->helperText('The API key to use.')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->updateCodeSnippet($get, $set))
                        ->createOptionForm(fn ($form) => PassportTokenResource::form($form))
                        ->options(fn () => PassportToken::all()->pluck('name', 'token')),
                    Checkbox::make('dark_mode')
                        ->helperText('Check to enable dark mode.')
                        ->label('Dark Mode')
                        ->live()
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->updateCodeSnippet($get, $set)),
                    TorchlightCode::make('widget_code')
                        ->helperText('Copy the code above and paste it into your website.')
                        ->default(fn () => $this->generateCodeSnippet())
                        ->hiddenLabel()
                        ->language('html'),
                    WidgetCodeGenerator::make('copy')
                        ->default(fn () => $this->generateCodeSnippet())
                        ->label('Copy Code')
                        ->color('gray'),
                ]),
        ];
    }

    protected function updateCodeSnippet(Get $get, Set $set): void
    {
        $code = $this->generateCodeSnippet(
            widget: $get('widget'),
            apiKey: $get('api_key'),
            darkMode: $get('dark_mode') ? 'true' : 'false'
        );

        $set('widget_code', $code);

        $this->dispatch('update-code', code: $code);
    }

    protected function generateCodeSnippet(?string $widget = null, ?string $apiKey = null, string $darkMode = 'false'): string
    {
        return <<<HTML
<div id="perscom_widget_wrapper">
    <script
        id="perscom_widget"
        data-apikey="$apiKey"
        data-widget="$widget"
        data-dark="$darkMode"
        src="https://widget.perscom.io/widget.js"
        type="text/javascript"
    ></script>
</div>
HTML;
    }
}
