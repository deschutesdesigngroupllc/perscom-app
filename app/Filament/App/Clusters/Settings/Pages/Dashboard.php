<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Actions\ResetTenantSubdomain;
use App\Actions\UpdateTenantSubdomain;
use App\Filament\App\Clusters\Settings;
use App\Models\Enums\RosterMode;
use App\Models\Tenant;
use App\Rules\SubdomainRule;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Panel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;
use Throwable;

class Dashboard extends SettingsPage
{
    protected static ?string $cluster = Settings::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    protected static string $settings = DashboardSettings::class;

    protected static ?string $title = 'Dashboard Settings';

    protected ?string $subheading = 'Settings that focus on configuring and setting up your online dashboard.';

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return parent::canAccess()
            && Auth::user()->hasRole(Utils::getSuperAdminName())
            && ! App::isDemo();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Home Page')
                            ->icon('heroicon-o-home')
                            ->schema([
                                TextInput::make('title')
                                    ->maxLength(255)
                                    ->required()
                                    ->helperText('This is the main heading that will be displayed on the dashboard home page.'),
                                TextInput::make('subtitle')
                                    ->maxLength(255)
                                    ->helperText('This is the subheading text that will be displayed under the main heading on the dashboard home page.'),
                            ]),
                        Tabs\Tab::make('Domain')
                            ->icon('heroicon-o-globe-alt')
                            ->badgeColor('gray')
                            ->badge(function () {
                                /** @var Tenant $tenant */
                                $tenant = Filament::getTenant();

                                return $tenant->url;
                            })
                            ->schema([
                                Placeholder::make('fallback')
                                    ->content(function (): HtmlString {
                                        /** @var Tenant $tenant */
                                        $tenant = Filament::getTenant();

                                        return new HtmlString("<a href='$tenant->fallback_url' target='_blank'>$tenant->fallback_url</a>");
                                    })
                                    ->helperText('The default URL for your account. You can always access your dashboard from this domain.'),
                                TextInput::make('subdomain')
                                    ->maxLength(255)
                                    ->rules(function (): array {
                                        /** @var Tenant $tenant */
                                        $tenant = Filament::getTenant();

                                        return [Rule::unique('mysql.domains', 'domain')->ignore($tenant->custom_domain->id ?? null), new SubdomainRule];
                                    })
                                    ->helperText('The subdomain for your account. You will be redirected to your new domain if this field is updated when the form is saved. Please understand your account will no longer be accessible using the the domain you are currently using after changing this setting.')
                                    ->prefix(config('app.scheme').'://')
                                    ->suffix(config('app.base_url'))
                                    ->suffixAction(Action::make('test')
                                        ->label('Reset')
                                        ->icon('heroicon-o-arrow-path')
                                        ->color('primary')
                                        ->requiresConfirmation()
                                        ->modalDescription('Are you sure you want to reset your subdomain? This means your dashboard will no longer be accessible by your custom subdomain. You will only be able to reach your dashboard using your fallback URL from this point on.')
                                        ->action(fn () => $this->resetTenantSubdomain())
                                        ->tooltip('Reset subdomain')
                                    ),
                            ]),
                        Tabs\Tab::make('User Profile')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('cover_photo_height')
                                    ->label('Cover Photo Height')
                                    ->required()
                                    ->helperText('The pixel height of a cover photo in a user\'s profile.')
                                    ->numeric()
                                    ->suffix('px')
                                    ->default(250),
                                Select::make('user_hidden_fields')
                                    ->label('Hidden Fields')
                                    ->helperText('Choose from a list of user fields that should be hidden from their user profile.')
                                    ->multiple()
                                    ->options(collect([
                                        'approved' => 'Approval Status',
                                        'assignment_records' => 'Assignment Records',
                                        'award_records' => 'Award Records',
                                        'combat_records' => 'Combat Records',
                                        'cover_photo' => 'Cover Photo',
                                        'created_at' => 'Joined At',
                                        'email' => 'Email',
                                        'email_verified_at' => 'Email Verification Date',
                                        'last_assignment_change_date' => 'Last Assignment Change Date',
                                        'last_rank_change_date' => 'Last Rank Change Date',
                                        'last_seen_at' => 'Last Online At',
                                        'name' => 'Name',
                                        'online' => 'Online Status',
                                        'phone_number' => 'Phone Number',
                                        'position_id' => 'Position',
                                        'profile_photo' => 'Profile Photo',
                                        'qualification_records' => 'Qualification Records',
                                        'rank_id' => 'Rank',
                                        'rank_records' => 'Rank Records',
                                        'secondary_assignment_records' => 'Secondary Assignment Records',
                                        'service_records' => 'Service Records',
                                        'specialty_id' => 'Specialty',
                                        'status_id' => 'Status',
                                        'time_in_assignment' => 'Time In Assignment',
                                        'time_in_grade' => 'Time In Grade',
                                        'time_in_service' => 'Time In Service',
                                        'timezone' => 'Timezone',
                                        'training_records' => 'Training Records',
                                        'unit_id' => 'Unit',
                                        'updated_at' => 'Last Updated At',
                                    ])->sort()->toArray()),
                            ]),
                        Tabs\Tab::make('Roster')
                            ->icon('heroicon-o-queue-list')
                            ->schema([
                                Radio::make('roster_mode')
                                    ->label('Mode')
                                    ->required()
                                    ->live()
                                    ->options(RosterMode::class),
                                Repeater::make('roster_sort_order')
                                    ->label('Sort')
                                    ->reorderable()
                                    ->addActionLabel('Add New Field')
                                    ->helperText('Choose the order in which the users on the roster are sorted under their respective unit or slot. Items on the top will be treated as having the most priority.')
                                    ->simple(Select::make('roster_sort_order')
                                        ->distinct()
                                        ->options([
                                            'ranks.order' => 'Rank',
                                            'positions.order' => 'Position',
                                            'specialties.order' => 'Specialty',
                                            'users.name' => 'Name',
                                        ])
                                    ),
                            ]),
                    ]),
            ]);
    }

    /**
     * @throws Throwable
     */
    protected function afterSave(): void
    {
        /** @var array $data */
        $data = data_get($this->form->getLivewire(), 'data');

        /** @var Tenant $tenant */
        $tenant = Filament::getTenant();

        $subdomain = data_get($data, 'subdomain');

        if (filled($subdomain) && $subdomain !== $tenant->custom_domain?->domain) {
            $this->updateTenantSubdomain($subdomain);
        } elseif (blank($subdomain) && filled($tenant->custom_domain)) {
            $this->resetTenantSubdomain();
        }

        SettingsService::flush(DashboardSettings::class);
    }

    /**
     * @throws Throwable
     */
    private function updateTenantSubdomain(string $subdomain): void
    {
        /** @var Tenant $tenant */
        $tenant = Filament::getTenant();

        $action = new UpdateTenantSubdomain;
        $action->handle($tenant, $subdomain);

        Notification::make()
            ->success()
            ->title('Your subdomain has been successfully set.')
            ->send();
    }

    /**
     * @throws Throwable
     */
    private function resetTenantSubdomain(): void
    {
        /** @var Tenant $tenant */
        $tenant = Filament::getTenant();

        $action = new ResetTenantSubdomain;
        $action->handle($tenant);

        Notification::make()
            ->success()
            ->title('Your subdomain has been successfully removed.')
            ->send();
    }
}
