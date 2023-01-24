<?php

namespace App\Nova;

use App\Nova\Actions\Passport\RegenerateClientSecret;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;

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
        return 'Application';
    }

    /**
     * @param  NovaRequest  $request
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
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Name')->rules('required')->sortable(),
            ID::make('Client ID', 'id')->hide()->sortable(),
            Text::make('Client ID', function () {
                return $this->id;
            })->copyable()->exceptOnForms(),
            Hidden::make('Secret')->default(Str::random(40)),
            Text::make('Client Secret', 'secret')->readonly()->copyable()->onlyOnDetail(),
            URL::make('Redirect URL', 'redirect')->rules('required'),
            Boolean::make('Revoked')->default(false)->sortable()->hideWhenCreating()->showOnUpdating()->sortable(),
            Heading::make('OAuth 2.0 Endpoints')->onlyOnDetail(),
            Text::make('Authorization Endpoint', function () {
                return route('passport.authorizations.authorize');
            })->copyable()->onlyOnDetail(),
            Text::make('Token Endpoint', function () {
                return route('passport.token');
            })->copyable()->onlyOnDetail(),
            Text::make('Refresh Token Endpoint', function () {
                return route('passport.token.refresh');
            })->copyable()->onlyOnDetail(),
            Text::make('Authenticated User Endpoint', function () {
                return route('api.me.index');
            })->copyable()->onlyOnDetail(),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->sortable()->exceptOnForms(),
            DateTime::make('Updated At')->onlyOnDetail(),
            HasMany::make('Authorized Applications', 'tokens', PassportAuthorizedApplications::class),
        ];
    }

    /**
     * @param  Request  $request
     * @return false
     */
    public function authorizedToReplicate(Request $request)
    {
        return false;
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [(new RegenerateClientSecret())->onlyOnDetail()];
    }
}
