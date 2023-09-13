<?php

namespace App\Nova;

use App\Nova\Actions\Passport\RegenerateClientSecret;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphOne;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Passport\Passport;

class PassportClient extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\PassportClient::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id'];

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'applications';
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return 'Applications';
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('name', '<>', 'Default Personal Access Client')
            ->where('name', '<>', 'Default Password Grant Client');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Name')
                ->rules('required')
                ->sortable(),
            ID::make('Client ID', 'id')
                ->hide()
                ->sortable(),
            Text::make('Client ID', function () {
                return $this->id;
            })->copyable()
                ->exceptOnForms(),
            Hidden::make('Secret')
                ->default(Str::random(40)),
            Text::make('Client Secret', 'secret')
                ->readonly()
                ->copyable()
                ->onlyOnDetail(),
            Textarea::make('Description')
                ->alwaysShow()
                ->help('The description will also show on the authorization screen when a client attempts to authenticate.')
                ->nullable(),
            MultiSelect::make('Scopes')->options(Passport::scopes()->mapWithKeys(function ($scope) {
                return [$scope->id => $scope->id];
            })
                ->sort())
                ->help('The scopes the client may request. Leave blank to allow access to all scopes.')
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
                    ->rules('required'),
                Text::make('Redirect URL', function () {
                    return $this->redirect;
                })
                    ->copyable()
                    ->onlyOnDetail(),
                URL::make('Logout URL', 'logout')
                    ->onlyOnForms()
                    ->help('The URL PERSCOM can redirect a user to after completing the logout in PERSCOM. See documentation on how to implement a post logout redirect.'),
                Text::make('Logout URL', function () {
                    return $this->logout;
                })
                    ->copyable()
                    ->onlyOnDetail(),
            ]),
            Panel::make('Application Endpoints', [
                Text::make('Discovery Endpoint', function () {
                    return route('oidc.discovery');
                })->copyable()->onlyOnDetail(),
                Text::make('Authorization Endpoint', function () {
                    return route('passport.authorizations.authorize');
                })->copyable()->onlyOnDetail(),
                Text::make('Token Endpoint', function () {
                    return route('passport.token');
                })->copyable()->onlyOnDetail(),
                Text::make('Logout Endpoint', function () {
                    return route('oidc.logout');
                })->copyable()->onlyOnDetail(),
                Text::make('User Info Endpoint', function () {
                    return route('oidc.userinfo');
                })->copyable()->onlyOnDetail(),
            ]),
            HasMany::make('Authorized Clients', 'tokens', PassportAuthorizedClients::class),
        ];
    }

    /**
     * @return false
     */
    public function authorizedToReplicate(Request $request)
    {
        return false;
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [(new RegenerateClientSecret())->onlyOnDetail()];
    }
}
