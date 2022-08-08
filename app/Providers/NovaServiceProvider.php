<?php

namespace App\Providers;

use App\Nova\ActionEvent;
use App\Nova\Admin as AdminResource;
use App\Nova\Announcement;
use App\Nova\Award;
use App\Nova\Dashboards\Admin;
use App\Nova\Dashboards\Main;
use App\Nova\Document;
use App\Nova\Domain;
use App\Nova\Field;
use App\Nova\Forms\Form;
use App\Nova\Forms\Submission;
use App\Nova\Lenses\CurrentUsersSubmissions;
use App\Nova\Passport\Client;
use App\Nova\Passport\Token;
use App\Nova\Permission;
use App\Nova\Position;
use App\Nova\Qualification;
use App\Nova\Rank;
use App\Nova\Records\Assignment as AssignmentRecords;
use App\Nova\Records\Award as AwardRecords;
use App\Nova\Records\Combat as CombatRecords;
use App\Nova\Records\Qualification as QualificationRecords;
use App\Nova\Records\Rank as RankRecords;
use App\Nova\Records\Service;
use App\Nova\Records\Service as ServiceRecords;
use App\Nova\Role;
use App\Nova\Specialty;
use App\Nova\Status;
use App\Nova\Subscription;
use App\Nova\Tenant;
//use App\Nova\TenantUser;
use App\Nova\Unit;
use App\Nova\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Spatie\Url\Url;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
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
            config()->set(
                'nova.middleware',
                $middleware
                    ->reject(function ($middleware) {
                        return $middleware === 'verified';
                    })
                    ->toArray()
            );
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        if (\Illuminate\Support\Facades\Request::isCentralRequest()) {
            Nova::mainMenu(function (Request $request) {
                return [
                    MenuSection::dashboard(Admin::class)->icon('chart-bar'),
                    MenuSection::make('Application', [
                        MenuItem::resource(AdminResource::class),
                        MenuItem::resource(Domain::class),
                        MenuItem::resource(Subscription::class),
                        MenuItem::resource(Tenant::class),
//                        MenuItem::resource(TenantUser::class)->name('Users'),
                    ])
                        ->icon('terminal')
                        ->collapsable(),

                    MenuSection::make('Tools', [
                        MenuItem::externalLink(
                            'Horizon',
                            Url::fromString(config('app.url') . '/' . config('horizon.path'))
                                ->withScheme(app()->environment() === 'production' ? 'https' : 'http')
                                ->__toString()
                        ),
                        MenuItem::externalLink(
                            'Telescope',
                            Url::fromString(config('app.url') . '/' . config('telescope.path'))
                                ->withScheme(app()->environment() === 'production' ? 'https' : 'http')
                                ->__toString()
                        ),
                    ])
                        ->icon('external-link')
                        ->collapsable(),
                ];
            });
        } else {
            Nova::mainMenu(function (Request $request) {
                return [
                    MenuSection::dashboard(Main::class)->icon('chart-bar'),

                    MenuSection::make('Account', [
                        MenuItem::link(
                            'My Personnel File',
                            route(
                                'nova.pages.detail',
                                [
                                    'resource' => User::uriKey(),
                                    'resourceId' => Auth::user()->getAuthIdentifier(),
                                ],
                                false
                            )
                        ),
                        MenuItem::lens(Submission::class, CurrentUsersSubmissions::class),
                        MenuItem::link(
                            'New Form Submission',
                            route(
                                'nova.pages.create',
                                [
                                    'resource' => Submission::uriKey(),
                                    'viaResource' => 'users',
                                    'viaResourceId' => Auth::user()->getAuthIdentifier(),
                                    'viaRelationship' => 'submissions',
                                    'relationshipType' => 'hasMany',
                                ],
                                false
                            )
                        ),
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
                    ])
                        ->icon('office-building')
                        ->collapsable(),

                    MenuSection::make('Forms', [
                        MenuItem::resource(Form::class)->canSee(function (NovaRequest $request) {
                            return $request->user()->hasPermissionTo('view:form');
                        }),
                        MenuItem::resource(Submission::class)->canSee(function (NovaRequest $request) {
                            return $request->user()->hasPermissionTo('view:submission');
                        }),
                    ])
                        ->icon('pencil-alt')
                        ->collapsable(),

                    MenuSection::make('Records', [
                        MenuItem::resource(AssignmentRecords::class),
                        MenuItem::resource(AwardRecords::class),
                        MenuItem::resource(CombatRecords::class),
                        MenuItem::resource(QualificationRecords::class),
                        MenuItem::resource(RankRecords::class),
                        MenuItem::resource(ServiceRecords::class),
                    ])
                        ->icon('document-text')
                        ->collapsable(),

//                    MenuSection::make('External Integration', [
//                        MenuGroup::make('OAuth 2.0', [MenuItem::resource(Client::class)]),
//                        MenuGroup::make('API', [MenuItem::resource(Token::class)]),
//                    ])
//                        ->icon('link')
//                        ->collapsable(),

                    MenuSection::make('Settings', [
                        MenuItem::resource(Field::class),
                        MenuItem::resource(ActionEvent::class),
                        MenuItem::resource(Permission::class),
                        MenuItem::resource(Role::class),
                    ])
                        ->icon('cog')
                        ->collapsable(),

                    MenuSection::make('Support', [
                        MenuItem::externalLink('Community Forums', 'https://community.deschutesdesigngroup.com'),
                        MenuItem::externalLink('Help Desk', 'https://support.deschutesdesigngroup.com'),
                        MenuItem::externalLink(
                            'Submit A Ticket',
                            'https://support.deschutesdesigngroup.com/hc/en-us/requests/new'
                        ),
                        MenuItem::externalLink(
                            'Suggest A Feature',
                            'https://community.deschutesdesigngroup.com/forum/3-feedback-and-ideas/'
                        ),
                    ])->icon('support'),
                ];
            });
        }

        Nova::userMenu(function (Request $request, Menu $menu) {
            if (\Illuminate\Support\Facades\Request::isCentralRequest()) {
                $menu->append([
                    MenuItem::externalLink(
                        'Account',
                        route('nova.pages.detail', [
                            'resource' => \App\Nova\Admin::uriKey(),
                            'resourceId' => Auth::user()->getAuthIdentifier(),
                        ])
                    ),
                ]);
            } else {
                $menu->append([
                    MenuItem::externalLink(
                        'My Personnel File',
                        route('nova.pages.detail', [
                            'resource' => User::uriKey(),
                            'resourceId' => Auth::user()->getAuthIdentifier(),
                        ])
                    ),
                ]);
                // TODO: Enable on go live date
                //                if (!Request::isDemoMode()) {
                //                    $menu->append([
                //                        MenuItem::externalLink('Billing', route('spark.portal'))->canSee(function (
                //                            NovaRequest $request
                //                        ) {
                //                            return $request->user()->hasPermissionTo('manage:billing');
                //                        }),
                //                    ]);
                //                }
            }
            $menu->append([
                MenuItem::make('Logout', 'logout')->method('POST', [
                    '_token' => csrf_token(),
                ]),
            ]);
            return $menu;
        });

        Nova::footer(function ($request) {
            return Blade::render('
	            <div class="mt-8 leading-normal text-xs text-gray-500 space-y-1"><p class="text-center">PERSCOM Personnel Management System</a> · v{{ config("app.version") }} ({{ \Illuminate\Support\Str::ucfirst(config("app.env")) }})</p>
            		<p class="text-center">© 2022 Deschutes Design Group LLC</p>
        		</div>
	        ');
        });
    }

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
     *
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
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }
}
