<?php

namespace App\Nova;

use App\Facades\Feature;
use App\Models\Enums\FeatureIdentifier;
use App\Models\PassportToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

class PassportAuthorizedApplications extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = PassportToken::class;

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
    public static $search = ['id', 'name'];

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'authorized-applications';
    }

    /**
     * @return string
     */
    public static function label()
    {
        return 'Authorized Applications';
    }

    /**
     * @param  NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        $personalAccessClient = app()->make(ClientRepository::class)->personalAccessClient();

        return $query->where('client_id', '<>', $personalAccessClient?->id);
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
            BelongsTo::make('Application', 'client', PassportClient::class)->sortable()->readonly(),
            MultiSelect::make('Scopes')->options(Passport::scopes()->mapWithKeys(function ($scope) {
                return [$scope->id => $scope->id];
            })->sort())->hideFromIndex()->readonly(),
            Boolean::make('Revoked')->default(false)->sortable(),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')->sortable()->exceptOnForms(),
            DateTime::make('Updated At')->onlyOnDetail(),
            DateTime::make('Expires At')->sortable()->exceptOnForms(),
        ];
    }

    /**
     * @param  Request  $request
     * @return bool
     */
    public static function authorizedToViewAny(Request $request)
    {
        return Feature::isAccessible(FeatureIdentifier::FEATURE_SINGLE_SIGN_ON) && Gate::check('viewAny', PassportToken::class);
    }

    /**
     * @param  Request  $request
     * @return false
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    /**
     * @param  NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return '/resources/'.static::uriKey();
    }

    /**
     * @param  NovaRequest  $request
     * @param  Model  $model
     */
    public static function afterCreate(NovaRequest $request, Model $model)
    {
        Auth::user()->createToken($model->name, $model->scopes);
        $model->delete();
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
        return [];
    }
}
