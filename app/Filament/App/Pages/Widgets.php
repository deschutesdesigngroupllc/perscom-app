<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Filament\App\Resources\PassportTokenResource;
use App\Models\User;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\CodeEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Phiki\Grammar\Grammar;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;
use UnitEnum;

class Widgets extends Page
{
    use HasPageShield;

    public ?string $apiKey = null;

    #[Url]
    public string $widget = 'roster';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-code-bracket';

    protected static string|UnitEnum|null $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 9;

    protected string $view = 'filament.app.pages.widgets';

    protected ?string $subheading = 'Widgets offer a visual representation of your personnel data that can be embedded into any external website. Use the widget explorer below to interact with the widgets in real-time.';

    /**
     * @var array<string, string>
     */
    protected static array $widgets = [
        'awards' => 'Awards',
        'calendar' => 'Calendar',
        'forms' => 'Forms',
        'positions' => 'Positions',
        'qualifications' => 'Qualifications',
        'ranks' => 'Ranks',
        'roster' => 'Roster',
        'specialties' => 'Specialties',
    ];

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

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('switch')
                ->label('Choose Widget')
                ->color('gray')
                ->modalDescription('Choose the widget you would like to preview.')
                ->modalSubmitActionLabel('Preview Widget')
                ->modalCancelAction(false)
                ->modalCloseButton(false)
                ->closeModalByEscaping(false)
                ->closeModalByClickingAway(false)
                ->schema([
                    Select::make('widget')
                        ->required()
                        ->default($this->widget)
                        ->options(static::$widgets),
                ])
                ->action(function (array $data): void {
                    $this->widget = data_get($data, 'widget');
                    $this->redirect(Widgets::getUrl([
                        'widget' => $this->widget,
                    ]));
                }),
            Action::make('code')
                ->label('Get Widget Code')
                ->slideOver()
                ->modalDescription('Use the form below to configure and generate the code necessary to embed a widget in your website.')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->formId('widget-generator')
                ->schema([
                    Select::make('widget')
                        ->helperText('The widget to display.')
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->updateCodeSnippet($get, $set))
                        ->options(static::$widgets),
                    Select::make('api_key')
                        ->label('API Key')
                        ->helperText('The API key to use.')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->updateCodeSnippet($get, $set))
                        ->suffixAction(Action::make('open')
                            ->icon('heroicon-o-plus')
                            ->openUrlInNewTab()
                            ->url(PassportTokenResource::getUrl('create'))
                        )
                        ->options(fn () => PassportTokenResource::getEloquentQuery()->pluck('name', 'token')),
                    Checkbox::make('dark_mode')
                        ->helperText('Check to enable dark mode.')
                        ->label('Dark Mode')
                        ->live()
                        ->afterStateUpdated(fn (Set $set, Get $get) => $this->updateCodeSnippet($get, $set)),
                    CodeEntry::make('widget_code')
                        ->copyable()
                        ->grammar(Grammar::Javascript)
                        ->helperText('Click to copy the code above and paste it into your website.')
                        ->default(fn (): string => $this->generateCodeSnippet())
                        ->hiddenLabel(),
                ]),
        ];
    }

    protected function updateCodeSnippet(Get $get, Set $set): void
    {
        $code = $this->generateCodeSnippet(
            widget: $get('widget'),
            apiKey: $get('api_key'),
            darkMode: $get('dark_mode') ? 'true' : 'false',
        );

        $set('widget_code', $code);

        $this->dispatch('update-code', code: $code);
    }

    protected function generateCodeSnippet(?string $widget = null, ?string $apiKey = null, string $darkMode = 'false'): string
    {
        $widgetUrl = config('app.widget_url');

        return <<<HTML
<div id="perscom_widget_wrapper">
    <script
        id="perscom_widget"
        data-apikey="{$apiKey}"
        data-widget="{$widget}"
        data-dark="{$darkMode}"
        src="{$widgetUrl}"
        type="text/javascript"
    ></script>
</div>
HTML;
    }
}
