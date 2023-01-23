<?php

namespace App\Providers;

use App\Models\Forms\Submission as SubmissionModel;
use App\Nova\Action;
use App\Nova\Admin as AdminResource;
use App\Nova\Announcement;
use App\Nova\Award;
use App\Nova\Dashboards\Admin;
use App\Nova\Dashboards\Main;
use App\Nova\Document;
use App\Nova\Domain;
use App\Nova\Feature;
use App\Nova\Field;
use App\Nova\Forms\Form;
use App\Nova\Forms\Submission;
use App\Nova\Mail;
use App\Nova\Message;
use App\Nova\Passport\AuthorizedApplications;
use App\Nova\Passport\Client;
use App\Nova\Passport\Log;
use App\Nova\Passport\PersonalAccessToken;
use App\Nova\Permission;
use App\Nova\Position;
use App\Nova\Qualification;
use App\Nova\Rank;
use App\Nova\Records\Assignment as AssignmentRecords;
use App\Nova\Records\Award as AwardRecords;
use App\Nova\Records\Combat as CombatRecords;
use App\Nova\Records\Qualification as QualificationRecords;
use App\Nova\Records\Rank as RankRecords;
use App\Nova\Records\Service as ServiceRecords;
use App\Nova\Role;
use App\Nova\Specialty;
use App\Nova\Status;
use App\Nova\Subscription;
use App\Nova\Tenant;
use App\Nova\Unit;
use App\Nova\User;
use Codinglabs\FeatureFlags\Facades\FeatureFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Timezone;
use Laravel\Nova\Http\Controllers\ResourceDestroyController;
use Laravel\Nova\Http\Controllers\ResourceStoreController;
use Laravel\Nova\Http\Controllers\ResourceUpdateController;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Panel;
use Outl1ne\NovaSettings\NovaSettings;
use Perscom\Roster\Roster;
use Spatie\Url\Url;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()->register();
    }

    /**
     * Register the Nova gate.
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return true;
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        if (Request::isCentralRequest()) {
            return [new Admin()];
        }

        return [new Main()];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (Request::isCentralRequest()) {
            config()->set('nova.path', '/admin');
            config()->set('nova.guard', 'admin');
            config()->set('nova.passwords', 'admins');
        }

        if (Request::isDemoMode()) {
            $middleware = collect(config('nova.middleware'));
            config()->set('nova.middleware', $middleware->reject(function ($middleware) {
                return $middleware === 'verified';
            })->toArray());
        }

        $this->app->bind(ResourceStoreController::class, \App\Http\Controllers\Nova\ResourceStoreController::class);
        $this->app->bind(ResourceUpdateController::class, \App\Http\Controllers\Nova\ResourceUpdateController::class);
        $this->app->bind(ResourceDestroyController::class, \App\Http\Controllers\Nova\ResourceDestroyController::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
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
                        MenuItem::resource(Feature::class),
                        MenuItem::resource(Subscription::class),
                        MenuItem::resource(Tenant::class),
                    ])->icon('terminal')->collapsable(),

                    MenuSection::make('Communications', [
                        MenuItem::resource(Mail::class),
                        MenuItem::resource(Message::class),
                    ])->icon('chat')->collapsable(),

                    MenuSection::make('Tools', [
                        MenuItem::externalLink('Horizon', Url::fromString(config('app.url').
                                                                          '/'.
                                                                          config('horizon.path'))
                                                             ->withScheme(config('app.scheme'))
                                                             ->__toString()),
                        MenuItem::externalLink('Telescope', Url::fromString(config('app.url').
                                                                            '/'.
                                                                            config('telescope.path'))
                                                               ->withScheme(config('app.scheme'))
                                                               ->__toString()),
                    ])->icon('external-link')->collapsable(),
                ];
            });
        } else {
            Nova::mainMenu(function (Request $request) {
                return [
                    MenuSection::dashboard(Main::class)->icon('chart-bar'),

                    MenuSection::make('Roster')->path('/roster')->icon('user-group'),

                    MenuSection::make('Account', [
                        MenuItem::link('My Personnel File', route('nova.pages.detail', [
                            'resource' => User::uriKey(),
                            'resourceId' => Auth::user()->getAuthIdentifier(),
                        ], false)),
                    ])->icon('user-circle'),

                    MenuSection::make('Organization', [
                        MenuItem::resource(Announcement::class),
                        MenuItem::resource(Award::class),
                        MenuItem::resource(Document::class),
                        MenuItem::resource(Position::class),
                        MenuItem::resource(Qualification::class),
                        MenuItem::resource(Rank::class),
                        MenuItem::resource(Specialty::class),
                        MenuItem::resource(Status::class),
                        MenuItem::resource(Unit::class),
                        MenuItem::resource(User::class),
                    ])->icon('office-building')->collapsable(),

                    MenuSection::make('Forms', [
                        MenuItem::resource(Field::class),
                        MenuItem::resource(Form::class),
                        MenuItem::resource(Submission::class)->withBadgeIf(function () {
                            if (Auth::user()->hasPermissionTo('update:submission')) {
                                return SubmissionModel::query()->whereDoesntHave('statuses')->count();
                            }

                            return SubmissionModel::query()
                                                  ->where('user_id', '=', Auth::user()->getAuthIdentifier())
                                                  ->whereDoesntHave('statuses')
                                                  ->count();
                        }, 'info', function () {
                            return SubmissionModel::query()->whereDoesntHave('statuses')->exists();
                        }),
                    ])->icon('pencil-alt')->collapsable(),

                    MenuSection::make('Records', [
                        MenuItem::resource(AssignmentRecords::class),
                        MenuItem::resource(AwardRecords::class),
                        MenuItem::resource(CombatRecords::class),
                        MenuItem::resource(QualificationRecords::class),
                        MenuItem::resource(RankRecords::class),
                        MenuItem::resource(ServiceRecords::class),
                    ])->icon('document-text')->collapsable(),

                    MenuSection::make('External Integration', [
                        MenuItem::resource(AuthorizedApplications::class),
                        MenuItem::resource(Client::class)->name('My Apps'),
                        MenuItem::resource(PersonalAccessToken::class),
                        MenuItem::externalLink('Documentation', config('app.url').'/documentation/api')
                                ->openInNewTab()
                                ->canSee(function () {
                                    return Auth::user()->hasPermissionTo('manage:api');
                                }),
                        MenuItem::resource(Log::class),
                    ])->icon('link')->collapsable()->canSee(function () {
                        return \tenant()->canAccessApi();
                    }),

                    MenuSection::make('System', [
                        MenuItem::resource(Action::class),
                        MenuItem::resource(Permission::class),
                        MenuItem::resource(Role::class),
                        MenuGroup::make('Settings', [
                            MenuItem::link('General', '/settings/general'),
                            MenuItem::link('Localization', '/settings/localization'),
                        ])->collapsable(),
                    ])->icon('terminal')->collapsable()->canSee(function (NovaRequest $request) {
                        return ! $request->isDemoMode() && Auth::user()->hasRole('Admin');
                    })->collapsable()->collapsedByDefault(),

                    MenuSection::make('Support', [
                        MenuItem::externalLink('Community Forums', 'https://community.deschutesdesigngroup.com')
                                ->openInNewTab(),
                        MenuItem::externalLink('Help Desk', 'https://support.deschutesdesigngroup.com')->openInNewTab(),
                        MenuItem::externalLink('Submit A Ticket', 'https://support.deschutesdesigngroup.com/hc/en-us/requests/new')
                                ->openInNewTab(),
                        MenuItem::externalLink('Suggest A Feature', 'https://community.deschutesdesigngroup.com/forum/3-feedback-and-ideas/')
                                ->openInNewTab(),
                    ])->icon('support')->collapsable()->collapsedByDefault(),
                ];
            });
        }

        Nova::userMenu(function (Request $request, Menu $menu) {
            return [
                MenuItem::externalLink('Account', route('nova.pages.detail', [
                    'resource' => \App\Nova\Admin::uriKey(),
                    'resourceId' => Auth::user()->getAuthIdentifier(),
                ]))->canSee(function (NovaRequest $request) {
                    return $request->isCentralRequest();
                }),
                MenuItem::externalLink('My Personnel File', route('nova.pages.detail', [
                    'resource' => User::uriKey(),
                    'resourceId' => Auth::user()->getAuthIdentifier(),
                ]))->canSee(function (NovaRequest $request) {
                    return ! $request->isCentralRequest();
                }),
                MenuItem::externalLink('Billing', route('spark.portal'))->canSee(function (NovaRequest $request) {
                    return ! $request->isDemoMode() &&
                           ! $request->isCentralRequest() &&
                           $request->user()->hasPermissionTo('manage:billing') &&
                           FeatureFlag::isOn('billing');
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

        NovaSettings::addSettingsFields(function () {
            return [
                Panel::make('Account', [
                    Text::make('Account ID', function () {
                        return \tenant()->getTenantKey();
                    })->help('Your Account ID that must be used in all API requests.')->readonly(),
                    Text::make('Organization', 'organization')
                        ->help('The name of your organization.')
                        ->rules('required', 'string', 'max:255', Rule::unique(\App\Models\Tenant::class, 'name')
                                                                     ->ignore(\tenant()->getTenantKey()))
                        ->resolveUsing(function () {
                            return \tenant('name');
                        }),
                    Email::make('Email', 'email')
                         ->help('The main email account associated with the account. This email will receive all pertinent emails regarding PERSCOM.')
                         ->rules('required', 'string', 'email', 'max:255', Rule::unique(\App\Models\Tenant::class, 'email')
                                                                               ->ignore(\tenant()->getTenantKey()))
                         ->resolveUsing(function () {
                             return \tenant('email');
                         }),
                    Timezone::make('Default Timezone', 'timezone')
                            ->help('Choose the default timezone for your organization. If not set, the timezone will be set to UTC.'),
                ]),
                Panel::make('Domain', [
                    Text::make('Subdomain', 'subdomain')
                        ->copyable()
                        ->help('The subdomain for your account. You will be redirected to your new domain if this field is updated when the form is saved. Please understand your account will no longer be accessible using the the domain you are currently using after changing this setting.')
                        ->rules('required', 'string', 'max:255', 'alpha_dash', 'lowercase', Rule::unique(\App\Models\Domain::class, 'domain')
                                                                                                ->ignore(\tenant()->getTenantKey(), 'tenant_id'))
                        ->resolveUsing(function () {
                            return \tenant()->domain->domain;
                        })
                        ->canSee(function () {
                            return \tenant()->canAccessCustomSubdomain();
                        }),
                ]),
                Panel::make('Branding', [
                    Text::make('Dashboard Title', 'dashboard_title')
                        ->default(function () {
                            return \tenant('name');
                        })
                        ->help('The main heading on your dashboard homepage. This will default to your organization name if not set.'),
                    Text::make('Dashboard Subtitle', 'dashboard_subtitle')
                        ->help('A subtitle or description that can be added under your dashboard heading.'),
                ]),
            ];
        });

        NovaSettings::addSettingsFields([
            new Panel('Resources', [
                Text::make('Announcements (Plural)', 'localization_announcements')->placeholder('announcements'),
                Text::make('Awards (Plural)', 'localization_awards')->placeholder('awards'),
                Text::make('Documents (Plural)', 'localization_documents')->placeholder('documents'),
                Text::make('Positions (Plural)', 'localization_positions')->placeholder('positions'),
                Text::make('Qualifications (Plural)', 'localization_qualifications')->placeholder('qualifications'),
                Text::make('Ranks (Plural)', 'localization_ranks')->placeholder('ranks'),
                Text::make('Specialties (Plural)', 'localization_specialties')->placeholder('specialties'),
                Text::make('Statuses (Plural)', 'localization_statuses')->placeholder('statuses'),
                Text::make('Units (Plural)', 'localization_units')->placeholder('units'),
                Text::make('Users (Plural)', 'localization_users')->placeholder('users'),
            ]),
        ], [], 'Localization');
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            (new NovaSettings())->canSee(function () {
                return ! Request::isCentralRequest() && ! Request::isDemoMode() && Auth::user()->hasRole('Admin');
            }),
            (new Roster())->canSee(function () {
                return ! Request::isCentralRequest();
            }),
        ];
    }
}
