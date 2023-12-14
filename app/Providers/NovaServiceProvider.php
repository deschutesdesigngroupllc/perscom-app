<?php

namespace App\Providers;

use App\Features\ApiAccessFeature;
use App\Features\CustomSubDomainFeature;
use App\Features\SingleSignOnFeature;
use App\Features\SupportTicketFeature;
use App\Models\EventRegistration as EventRegistrationModel;
use App\Models\Submission as SubmissionModel;
use App\Models\TaskAssignment as TaskAssignmentModel;
use App\Nova\Action;
use App\Nova\Admin as AdminResource;
use App\Nova\Announcement;
use App\Nova\AssignmentRecord;
use App\Nova\Attachment;
use App\Nova\Award;
use App\Nova\AwardRecord;
use App\Nova\Banner;
use App\Nova\Calendar;
use App\Nova\CombatRecord;
use App\Nova\Dashboards\Admin;
use App\Nova\Dashboards\Main;
use App\Nova\Document;
use App\Nova\Domain;
use App\Nova\Event;
use App\Nova\EventRegistration;
use App\Nova\Feature as NovaFeature;
use App\Nova\Field;
use App\Nova\Form;
use App\Nova\Group;
use App\Nova\Image;
use App\Nova\Lenses\MyEvents;
use App\Nova\Lenses\MyTasks;
use App\Nova\Mail;
use App\Nova\Message;
use App\Nova\Newsfeed;
use App\Nova\PassportClient;
use App\Nova\PassportClientLog;
use App\Nova\PassportPersonalAccessToken;
use App\Nova\PassportPersonalAccessTokenLog;
use App\Nova\Permission;
use App\Nova\Position;
use App\Nova\Qualification;
use App\Nova\QualificationRecord;
use App\Nova\Rank;
use App\Nova\RankRecord;
use App\Nova\Receipt;
use App\Nova\Role;
use App\Nova\ServiceRecord;
use App\Nova\Specialty;
use App\Nova\Status;
use App\Nova\Submission;
use App\Nova\Subscription;
use App\Nova\Task;
use App\Nova\TaskAssignment;
use App\Nova\Tenant;
use App\Nova\Unit;
use App\Nova\User;
use App\Nova\Webhook;
use App\Rules\SubdomainRule;
use App\Services\FeatureOsService;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Timezone;
use Laravel\Nova\Http\Controllers\ResourceDestroyController;
use Laravel\Nova\Http\Controllers\ResourceStoreController;
use Laravel\Nova\Http\Controllers\ResourceUpdateController;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Pennant\Feature;
use Outl1ne\NovaSettings\NovaSettings;
use Perscom\Calendar\Calendar as CalendarWidget;
use Perscom\Forms\Forms as FormsWidget;
use Perscom\Roster\Roster as RosterWidget;
use Sentry\Laravel\Integration;
use Spatie\Url\Url;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    protected function routes(): void
    {
        Nova::routes()->register();
    }

    protected function gate(): void
    {
        Gate::define('viewNova', function ($user) {
            return true;
        });
    }

    /**
     * @return array<int, mixed>
     */
    protected function dashboards(): array
    {
        if (Request::isCentralRequest()) {
            return [new Admin()];
        }

        return [new Main()];
    }

    public function register(): void
    {
        Nova::style('nova-custom', Vite::asset('resources/css/nova.css'));
        Nova::ignoreMigrations();
        Nova::report(static function ($exception) {
            Integration::captureUnhandledException($exception);
        });

        if (Request::isCentralRequest()) {
            config()->set('nova.path', '/admin');
            config()->set('nova.guard', 'admin');
            config()->set('nova.passwords', 'admins');
        }

        if (Request::isDemoMode() || Request::isCentralRequest()) {
            $middleware = collect(config('nova.middleware'));
            config()->set('nova.middleware', $middleware->reject(function ($middleware) {
                return $middleware === 'verified' || $middleware === 'approved';
            })->toArray());
        }

        $this->app->bind(ResourceStoreController::class, \App\Http\Controllers\Nova\ResourceStoreController::class);
        $this->app->bind(ResourceUpdateController::class, \App\Http\Controllers\Nova\ResourceUpdateController::class);
        $this->app->bind(ResourceDestroyController::class, \App\Http\Controllers\Nova\ResourceDestroyController::class);
    }

    public function boot(): void
    {
        parent::boot();

        Nova::withBreadcrumbs();

        if (Request::isCentralRequest()) {
            Nova::mainMenu(function (Request $request) {
                return [
                    MenuSection::dashboard(Admin::class)->icon('chart-bar'),
                    MenuSection::make('Application', [
                        MenuItem::resource(AdminResource::class),
                        MenuItem::resource(Domain::class),
                        MenuItem::resource(NovaFeature::class),
                        MenuItem::resource(Receipt::class),
                        MenuItem::resource(Subscription::class),
                        MenuItem::resource(Tenant::class),
                    ])->icon('terminal')
                        ->collapsable(),

                    MenuSection::make('Communications', [
                        MenuItem::resource(Mail::class),
                        MenuItem::resource(Message::class),
                    ])->icon('chat')
                        ->collapsable(),

                    MenuSection::make('Tools', [
                        MenuItem::externalLink('Horizon', Url::fromString(config('app.url').'/'.config('horizon.path'))
                            ->withScheme(config('app.scheme'))
                            ->__toString())
                            ->openInNewTab(),
                        MenuItem::externalLink('Pulse', Url::fromString(config('app.url').'/'.config('pulse.path'))
                            ->withScheme(config('app.scheme'))
                            ->__toString())
                            ->openInNewTab(),
                        MenuItem::externalLink('Telescope', Url::fromString(config('app.url').'/'.config('telescope.path'))
                            ->withScheme(config('app.scheme'))
                            ->__toString())
                            ->openInNewTab(),
                    ])->icon('external-link')
                        ->collapsable(),

                    MenuSection::make('Website', [
                        MenuItem::resource(Banner::class),
                    ])->icon('desktop-computer')
                        ->collapsable(),
                ];
            });
        } else {
            Nova::mainMenu(function (Request $request) {
                return [
                    MenuSection::dashboard(Main::class)->icon('chart-bar'),

                    MenuSection::make('Calendar')->path('/calendar')->icon('calendar'),

                    MenuSection::make('Forms')->path('/forms')->icon('folder-open'),

                    MenuSection::make('Roster')->path('/roster')->icon('user-group'),

                    MenuSection::make('Account', [
                        MenuItem::link('My Personnel File', route('nova.pages.detail', [
                            'resource' => User::uriKey(),
                            'resourceId' => Auth::user()->getAuthIdentifier(),
                        ], false)),
                        MenuItem::lens(EventRegistration::class, MyEvents::class)
                            ->withBadge(function () {
                                return (string) EventRegistrationModel::query()->user(Auth::user())->future()->count();
                            }),
                        MenuItem::lens(TaskAssignment::class, MyTasks::class)
                            ->withBadge(function () {
                                return (string) TaskAssignmentModel::query()->user(Auth::user())->assigned()->count();
                            }),
                    ])->icon('user-circle'),

                    MenuSection::make('Calendar', [
                        MenuItem::resource(Calendar::class),
                        MenuItem::resource(Event::class),
                        MenuItem::resource(EventRegistration::class)
                            ->name('Registrations')
                            ->canSee(function () {
                                return Gate::check('create', EventRegistrationModel::class);
                            }),
                    ])->icon('calendar')
                        ->collapsable()
                        ->collapsedByDefault(),

                    MenuSection::make('Communications', [
                        MenuItem::resource(Mail::class),
                        MenuItem::resource(Newsfeed::class),
                    ])->icon('newspaper')
                        ->collapsable()
                        ->collapsedByDefault(),

                    MenuSection::make('Forms', [
                        MenuItem::resource(Form::class),
                        MenuItem::resource(Submission::class)
                            ->withBadge(function () {
                                return (string) SubmissionModel::query()->whereDoesntHave('statuses')->count();
                            }),
                    ])->icon('pencil-alt')
                        ->collapsable()
                        ->collapsedByDefault(),

                    MenuSection::make('Organization', [
                        MenuItem::resource(Announcement::class),
                        MenuItem::resource(Award::class),
                        MenuItem::resource(Document::class),
                        MenuItem::resource(Group::class),
                        MenuItem::resource(Position::class),
                        MenuItem::resource(Qualification::class),
                        MenuItem::resource(Rank::class),
                        MenuItem::resource(Specialty::class),
                        MenuItem::resource(Status::class),
                        MenuItem::resource(Task::class),
                        MenuItem::resource(Unit::class),
                    ])->icon('office-building')
                        ->collapsable()
                        ->collapsedByDefault(),

                    MenuSection::make('Personnel', [
                        MenuItem::resource(User::class),
                    ])->icon('users')
                        ->collapsable()
                        ->collapsedByDefault(),

                    MenuSection::make('Records', [
                        MenuItem::resource(AssignmentRecord::class),
                        MenuItem::resource(AwardRecord::class),
                        MenuItem::resource(CombatRecord::class),
                        MenuItem::resource(QualificationRecord::class),
                        MenuItem::resource(RankRecord::class),
                        MenuItem::resource(ServiceRecord::class),
                    ])->icon('document-text')
                        ->collapsable()
                        ->collapsedByDefault(),

                    MenuSection::make('System', [
                        MenuItem::resource(Attachment::class),
                        MenuItem::resource(Field::class),
                        MenuItem::resource(Image::class),
                        MenuItem::resource(Action::class),
                        MenuItem::resource(Permission::class),
                        MenuItem::resource(Role::class),
                        MenuItem::link('Settings', '/settings')->canSee(function (Request $request) {
                            return ! $request->isDemoMode() && Auth::user()->hasRole('Admin');
                        }),
                        MenuGroup::make('API', [
                            MenuItem::resource(PassportPersonalAccessToken::class)->name('Keys'),
                            MenuItem::resource(PassportPersonalAccessTokenLog::class),
                        ])->collapsable()
                            ->collapsedByDefault(),
                        MenuGroup::make('Integrations', [
                            MenuItem::resource(Webhook::class),
                            MenuItem::externalLink('Widgets', 'https://docs.perscom.io/external-integration/widgets')
                                ->openInNewTab()
                                ->canSee(function (Request $request) {
                                    return Gate::check('api', $request->user()) && Feature::active(ApiAccessFeature::class);
                                }),
                        ])->collapsable()
                            ->collapsedByDefault(),
                        MenuGroup::make('OAuth 2.0', [
                            MenuItem::resource(PassportClient::class)->name('Applications'),
                            MenuItem::resource(PassportClientLog::class),
                        ])->collapsable()
                            ->collapsedByDefault(),
                    ])->icon('terminal')->collapsable()->collapsedByDefault(),

                    MenuSection::make('Support', [
                        MenuItem::externalLink('Community Forums', 'https://community.deschutesdesigngroup.com')
                            ->openInNewTab(),
                        MenuItem::externalLink('Feature Requests', FeatureOsService::ssoRedirect('https://feedback.perscom.io/b/features-requests'))
                            ->openInNewTab(),
                        MenuItem::externalLink('Feedback', FeatureOsService::ssoRedirect('https://feedback.perscom.io/b/feedback'))
                            ->openInNewTab(),
                        MenuItem::externalLink('Documentation', 'https://docs.perscom.io')
                            ->openInNewTab(),
                        MenuItem::externalLink('Submit A Ticket', 'https://www.deschutesdesigngroup.com/support/new')
                            ->openInNewTab()
                            ->canSee(function () {
                                return Feature::active(SupportTicketFeature::class);
                            }),
                        MenuItem::externalLink('System Status', 'https://status.perscom.io')
                            ->openInNewTab(),
                    ])->icon('support')
                        ->collapsable()
                        ->collapsedByDefault(),
                ];
            });
        }

        Nova::userMenu(function (Request $request, Menu $menu) {
            return [
                MenuItem::externalLink('Account', route('nova.pages.detail', [
                    'resource' => \App\Nova\Admin::uriKey(),
                    'resourceId' => Auth::user()->getAuthIdentifier(),
                ]))->canSee(function (Request $request) {
                    return $request->isCentralRequest();
                }),
                MenuItem::externalLink('My Personnel File', route('nova.pages.detail', [
                    'resource' => User::uriKey(),
                    'resourceId' => Auth::user()->getAuthIdentifier(),
                ]))->canSee(function (Request $request) {
                    return ! $request->isCentralRequest();
                }),
                MenuItem::externalLink('Billing', route('spark.portal'))->canSee(function (Request $request) {
                    return ! $request->isDemoMode() &&
                           ! $request->isCentralRequest() &&
                           Gate::check('billing', $request->user());
                }),
                MenuItem::make('Logout', 'logout')->method('POST', [
                    '_token' => csrf_token(),
                ]),
            ];
        });

        Nova::footer(function ($request) {
            return Blade::render('
	            <div class="mt-8 leading-normal text-xs text-gray-500 space-y-1"><p class="text-center">{{ config("app.name") }}</a> · {{ config("app.version") }} ({{ Illuminate\Support\Str::ucfirst(config("app.env")) }})</p>
            		<p class="text-center">© {{ Illuminate\Support\Carbon::now()->year }} Deschutes Design Group LLC</p>
        		</div>
	        ');
        });

        NovaSettings::addSettingsFields(static function () {
            return [
                Tabs::make('Settings', [
                    Tab::make('Account', [
                        Text::make('PERSCOM ID', function () {
                            return \tenant()->getTenantKey();
                        })->help('Your PERSCOM ID that must be used in all external integrations.')->readonly(),
                        Text::make('Organization', 'organization')
                            ->help('The name of your organization.')
                            ->rules('required', 'string', 'max:255', Rule::unique(\App\Models\Tenant::class, 'name')->ignore(\tenant()->getTenantKey())),
                        Email::make('Email', 'email')
                            ->help('The main email account associated with the account. This email will receive all pertinent emails regarding PERSCOM.')
                            ->rules('required', 'string', 'email', 'max:255', Rule::unique(\App\Models\Tenant::class, 'email')->ignore(\tenant()->getTenantKey())),
                        Timezone::make('Default Timezone', 'timezone')
                            ->help('Choose the default timezone for your organization. If not set, the timezone will be set to UTC.')
                            ->rules('required'),
                    ]),
                    Tab::make('Domain', [
                        Text::make('Subdomain', 'subdomain')
                            ->copyable()
                            ->help('The subdomain for your account. You will be redirected to your new domain if this field is updated when the form is saved. Please understand your account will no longer be accessible using the the domain you are currently using after changing this setting.')
                            ->rules('string', 'max:255', 'alpha_dash', 'lowercase', Rule::unique(\App\Models\Domain::class, 'domain')->ignore(\tenant()->getTenantKey(), 'tenant_id'), new SubdomainRule())
                            ->canSee(function () {
                                return Feature::active(CustomSubDomainFeature::class);
                            }),
                    ]),
                    Tab::make('Branding', [
                        Text::make('Dashboard Title', 'dashboard_title')
                            ->help('The main heading on your dashboard homepage. This will default to your organization name if not set.'),
                        Text::make('Dashboard Subtitle', 'dashboard_subtitle')
                            ->help('A subtitle or description that can be added under your dashboard heading.'),
                    ]),
                    Tab::make('Localization', [
                        Text::make('Announcements (Plural)', 'localization_announcements')->placeholder('announcements'),
                        Text::make('Assignment', 'localization_assignment')->placeholder('assignment'),
                        Text::make('Awards (Plural)', 'localization_awards')->placeholder('awards'),
                        Text::make('Combat', 'localization_combat')->placeholder('combat'),
                        Text::make('Documents (Plural)', 'localization_documents')->placeholder('documents'),
                        Text::make('Positions (Plural)', 'localization_positions')->placeholder('positions'),
                        Text::make('Qualifications (Plural)', 'localization_qualifications')->placeholder('qualifications'),
                        Text::make('Ranks (Plural)', 'localization_ranks')->placeholder('ranks'),
                        Text::make('Service', 'localization_service')->placeholder('service'),
                        Text::make('Specialties (Plural)', 'localization_specialties')->placeholder('specialties'),
                        Text::make('Statuses (Plural)', 'localization_statuses')->placeholder('statuses'),
                        Text::make('Units (Plural)', 'localization_units')->placeholder('units'),
                        Text::make('Users (Plural)', 'localization_users')->placeholder('users'),
                    ]),
                    Tab::make('Registration', [
                        Boolean::make('Enabled', 'registration_enabled')->help('Deselect to disable registration.'),
                        Textarea::make('Disabled Message', 'registration_disabled_message')->help('Enter a message that will be provided when users attempt to register and registration is disabled.'),
                        Boolean::make('Admin Approval Required', 'registration_admin_approval_required')->help('Users can register for an account but will need admin approval to login.'),
                    ]),
                    Tab::make('Single Sign-On', [
                        Text::make('Single Sign-On Key', 'single_sign_on_key')
                            ->help('Use this Single Sign-On Key to sign JWT access tokens and access PERSCOM.io resources on the fly through the PERSCOM.io API.')
                            ->readonly()
                            ->copyable()
                            ->canSee(function () {
                                return Feature::active(SingleSignOnFeature::class);
                            }),
                    ]),
                    Tab::make('Users', [
                        MultiSelect::make('Default Permissions', 'default_permissions')->options(
                            \App\Models\Permission::all()->pluck('name', 'name')->sort()
                        )->help('The default permissions that will be given to new user accounts. Leave blank to assign no permissions.'),
                        MultiSelect::make('Default Roles', 'default_roles')->options(
                            \App\Models\Role::all()->pluck('name', 'name')->sort()
                        )->help('The default roles that will be given to new user accounts. Leave blank to assign no role.'),
                    ]),
                ])->showTitle(),
            ];
        }, [
            'registration_enabled' => 'boolean',
            'registration_admin_approval_required' => 'boolean',
            'default_permissions' => 'array',
            'default_roles' => 'array',
        ]);
    }

    /**
     * @return array<int, mixed>
     */
    public function tools(): array
    {
        return [
            (new NovaSettings())->canSee(function () {
                return ! Request::isCentralRequest() && ! Request::isDemoMode() && Auth::user()->hasRole('Admin');
            }),
            (new CalendarWidget())->canSee(function () {
                return ! Request::isCentralRequest();
            }),
            (new FormsWidget())->canSee(function () {
                return ! Request::isCentralRequest();
            }),
            (new RosterWidget())->canSee(function () {
                return ! Request::isCentralRequest();
            }),
        ];
    }
}
