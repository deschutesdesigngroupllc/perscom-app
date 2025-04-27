<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Filament\App\Resources\PassportTokenResource;
use App\Forms\Components\TorchlightCode;
use App\Forms\Components\WidgetCodeGenerator;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use DateTimeZone;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class Widgets extends Page
{
    use HasPageShield;

    public ?string $apiKey = null;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    protected static ?string $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.app.pages.widgets';

    protected ?string $subheading = 'Widgets offer a visual representation of your personnel data that can be embedded into any external website. Use the widget explorer below to interact with the widgets in real-time.';

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
                            'calendar' => 'Calendar',
                            'forms' => 'Forms',
                            'newsfeed' => 'Newsfeed',
                            'qualifications' => 'Qualifications',
                            'ranks' => 'Ranks',
                        ]),
                    Select::make('api_key')
                        ->label('API Key')
                        ->helperText('The API key to use.')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->updateCodeSnippet($get, $set))
                        ->suffixAction(FormAction::make('open')
                            ->icon('heroicon-o-plus')
                            ->openUrlInNewTab()
                            ->url(PassportTokenResource::getUrl('create'))
                        )
                        ->options(fn () => PassportTokenResource::getEloquentQuery()->pluck('name', 'token')),
                    Select::make('timezone')
                        ->preload()
                        ->searchable()
                        ->live()
                        ->default('UTC')
                        ->helperText('The timezone to use. By default all times are stored in UTC. Select a timezone to convert dates and times to your desired timezone.')
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->updateCodeSnippet($get, $set))
                        ->options(collect(DateTimeZone::listIdentifiers())->mapWithKeys(fn ($value, $key) => [$value => $value])),
                    Checkbox::make('dark_mode')
                        ->helperText('Check to enable dark mode.')
                        ->label('Dark Mode')
                        ->live()
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->updateCodeSnippet($get, $set)),
                    TorchlightCode::make('widget_code')
                        ->helperText('Copy the code above and paste it into your website.')
                        ->default(fn (): string => $this->generateCodeSnippet())
                        ->hiddenLabel()
                        ->language('html'),
                    WidgetCodeGenerator::make('copy')
                        ->default(fn (): string => $this->generateCodeSnippet())
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
            darkMode: $get('dark_mode') ? 'true' : 'false',
            timezone: $get('timezone'),
        );

        $set('widget_code', $code);

        $this->dispatch('update-code', code: $code);
    }

    protected function generateCodeSnippet(?string $widget = null, ?string $apiKey = null, string $darkMode = 'false', string $timezone = 'UTC'): string
    {
        return <<<HTML
<div id="perscom_widget_wrapper">
    <script
        id="perscom_widget"
        data-apikey="$apiKey"
        data-widget="$widget"
        data-dark="$darkMode"
        data-timezone="$timezone"
        src="https://widget.perscom.io/widget.js"
        type="text/javascript"
    ></script>
</div>
HTML;
    }
}
