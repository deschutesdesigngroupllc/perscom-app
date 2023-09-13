<?php

namespace App\Nova;

use App\Contracts\Passport\CreatesPersonalAccessToken;
use App\Models\PassportToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

class PassportPersonalAccessToken extends Resource
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
     * @var string[]
     */
    public static $orderBy = ['created_at' => 'desc'];

    /**
     * Get the text for the create resource button.
     *
     * @return string|null
     */
    public static function createButtonLabel()
    {
        return 'Create API Key';
    }

    /**
     * Get the text for the update resource button.
     *
     * @return string|null
     */
    public static function updateButtonLabel()
    {
        return 'Update API Key';
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return 'API Keys';
    }

    /**
     * @return string
     */
    public static function uriKey()
    {
        return 'api-keys';
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        $personalAccessClient = app()->make(ClientRepository::class)->personalAccessClient();

        return $query->where('client_id', '=', $personalAccessClient?->id);
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Hidden::make('ID', 'id')
                ->default('1'),
            Hidden::make('Client Id', 'client_id')
                ->default('1'),
            Text::make('Name')
                ->rules('required')
                ->sortable(),
            Text::make('API Key', function () {
                return Crypt::decryptString($this->token);
            })->displayUsing(function ($value) {
                return Str::limit($value, 50);
            })->onlyOnIndex()->readonly()->copyable(),
            Code::make('API Key', function () {
                return Crypt::decryptString($this->token);
            })
                ->readonly()
                ->language('shell')
                ->help('API Keys must be passed as Bearer tokens within the Authorization header of your HTTP request.'),
            MultiSelect::make('Scopes')->options(Passport::scopes()->mapWithKeys(function ($scope) {
                return [$scope->id => $scope->id];
            })
                ->sort())
                ->help('The scopes the API key has access to.')
                ->rules('required')
                ->hideFromIndex(),
            Boolean::make('Revoked')
                ->default(false)
                ->help('Check to prevent API access from this API key.')
                ->hideWhenCreating()
                ->showOnUpdating()
                ->sortable(),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Created At')
                ->sortable()
                ->exceptOnForms(),
            DateTime::make('Updated At')
                ->onlyOnDetail(),
            DateTime::make('Expires At')
                ->sortable()
                ->exceptOnForms(),
        ];
    }

    /**
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return '/resources/'.static::uriKey();
    }

    /**
     * @return void
     */
    public static function afterCreate(NovaRequest $request, Model $model)
    {
        $createPersonalAccessToken = app(CreatesPersonalAccessToken::class);
        $createPersonalAccessToken->create($request->user(), $model->name, $model->scopes);

        $model->delete();
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
        return [];
    }
}
