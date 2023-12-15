<?php

namespace App\Nova;

use App\Nova\Actions\Passport\RegenerateClientSecret;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphOne;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Passport\Passport;

class PassportClient extends Resource
{
    public static string $model = \App\Models\PassportClient::class;

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id'];

    public static function uriKey(): string
    {
        return 'applications';
    }

    public static function label(): string
    {
        return 'Applications';
    }

    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query->where('name', '<>', 'Default Personal Access Client')
            ->where('name', '<>', 'Default Password Grant Client');
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Text::make('Name')
                ->rules('required')
                ->sortable(),
            Select::make('Type')
                ->options([
                    'authorization_code' => 'Regular Web Application',
                    'implicit' => 'Single Page Web Applications or Native Applications',
                    'client_credentials' => 'Machine-to-Machine',
                    'password' => 'Resource Owner',
                ])
                ->onlyOnForms()
                ->default('authorization_code')
                ->required()
                ->help('Check out the <a href="https://docs.perscom.io/external-integration/oauth/oidc" target="_blank">documentation</a> on how to choose the correct application type.'),
            Badge::make('Type', function () {
                return $this->type;
            })
                ->map([
                    'authorization_code' => 'info',
                    'implicit' => 'warning',
                    'password' => 'danger',
                    'client_credentials' => 'success',
                ])
                ->label(function ($value) {
                    return match (true) {
                        $value === 'authorization_code' => 'Regular Web Application',
                        $value === 'implicit' => 'Single Page Web/Native Application',
                        $value === 'client_credentials' => 'Machine-to-Machine',
                        $value === 'password' => 'Resource Owner'
                    };
                })
                ->exceptOnForms(),
            Badge::make('Flow', function () {
                return $this->type;
            })
                ->map([
                    'authorization_code' => 'neutral',
                    'implicit' => 'neutral',
                    'password' => 'neutral',
                    'client_credentials' => 'neutral',
                ])
                ->label(function ($value) {
                    return Str::title(Str::replace('_', ' ', $value));
                })
                ->addTypes([
                    'neutral' => 'bg-gray-50 text-gray-600',
                ])
                ->exceptOnForms(),
            ID::make('Client ID', 'id')
                ->hide()
                ->sortable(),
            Text::make('Client ID', function () {
                return $this->id;
            })
                ->copyable()
                ->exceptOnForms(),
            Hidden::make('Secret')
                ->default(Str::random(40)),
            Text::make('Client Secret', 'secret')
                ->readonly()
                ->copyable()
                ->onlyOnDetail()
                ->canSee(function () {
                    return $this->type !== 'implicit';
                }),
            Textarea::make('Description')
                ->alwaysShow()
                ->help('The description will also show on the authorization screen when a client attempts to authenticate.')
                ->nullable(),
            MultiSelect::make('Scopes')
                ->options(
                    Passport::scopes()
                        ->pluck('id', 'id')
                        ->sort()
                )
                ->help('The scopes the client may request. Leave blank to allow access to all scopes.')
                ->nullValues(['', '0', 'null', '[]'])
                ->nullable()
                ->hideFromIndex(),
            Boolean::make('Revoked')
                ->default(false)
                ->help('Check to prevent access from this client.')
                ->sortable()
                ->hideWhenCreating()
                ->showOnUpdating()
                ->sortable(),
            MorphOne::make('Image', 'image'),
            new Panel('Application URI\'s', [
                URL::make('Redirect URL', 'redirect')
                    ->help('The URL PERSCOM will redirect the user back to after completing authentication.')
                    ->onlyOnForms()
                    ->dependsOn(['type'], function (URL $field, NovaRequest $request, FormData $formData) {
                        if ($formData->type !== 'authorization_code' || $formData !== 'implicit') {
                            $field->rules([])
                                ->hide();
                        }
                    })
                    ->rules('required'),
                Text::make('Redirect URL', function () {
                    return $this->redirect;
                })
                    ->canSee(function () {
                        return $this->type === 'authorization_code' || $this->type === 'implicit';
                    })
                    ->copyable()
                    ->onlyOnDetail(),
                URL::make('Logout URL', 'logout')
                    ->dependsOn(['type'], function (URL $field, NovaRequest $request, FormData $formData) {
                        if ($formData->type !== 'authorization_code' || $formData !== 'implicit') {
                            $field->rules([])
                                ->hide();
                        }
                    })
                    ->onlyOnForms()
                    ->help('The URL PERSCOM can redirect a user to after completing the logout in PERSCOM. See documentation on how to implement a post logout redirect.'),
                Text::make('Logout URL', function () {
                    return $this->logout;
                })
                    ->canSee(function () {
                        return $this->type === 'authorization_code' || $this->type === 'implicit';
                    })
                    ->copyable()
                    ->onlyOnDetail(),
            ]),
            Panel::make('Application Endpoints', [
                Text::make('Discovery Endpoint', function () {
                    return route('oidc.discovery');
                })
                    ->copyable()
                    ->onlyOnDetail(),
                Text::make('Authorization Endpoint', function () {
                    return route('passport.authorizations.authorize');
                })
                    ->copyable()
                    ->onlyOnDetail(),
                Text::make('Token Endpoint', function () {
                    return route('passport.token');
                })
                    ->copyable()
                    ->onlyOnDetail(),
                Text::make('Logout Endpoint', function () {
                    return route('oidc.logout');
                })
                    ->copyable()
                    ->onlyOnDetail(),
                Text::make('User Info Endpoint', function () {
                    return route('oidc.userinfo');
                })
                    ->copyable()
                    ->onlyOnDetail(),
            ]),
            HasMany::make('Authorized Clients', 'tokens', PassportAuthorizedClients::class),
        ];
    }

    public static function afterCreate(NovaRequest $request, Model $model): void
    {
        if ($model instanceof \App\Models\PassportClient) {
            if ($model->type === 'client_credentials') {
                $model->personal_access_client = false;
                $model->password_client = false;
                $model->save();
            } elseif ($model->type === 'password') {
                $model->personal_access_client = false;
                $model->password_client = true;
                $model->save();
            }
        }
    }

    public function authorizedToReplicate(Request $request): bool
    {
        return false;
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [(new RegenerateClientSecret())->onlyOnDetail()];
    }
}
