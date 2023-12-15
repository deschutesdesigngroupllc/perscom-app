<?php

namespace App\Nova;

use App\Contracts\Passport\CreatesPersonalAccessToken;
use App\Models\PassportToken;
use Illuminate\Database\Eloquent\Builder;
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
    public static string $model = PassportToken::class;

    public static array $orderBy = ['created_at' => 'desc'];

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var array
     */
    public static $search = ['id', 'name'];

    public static function createButtonLabel(): string
    {
        return 'Create API Key';
    }

    public static function updateButtonLabel(): string
    {
        return 'Update API Key';
    }

    public static function label(): string
    {
        return 'API Keys';
    }

    public static function uriKey(): string
    {
        return 'api-keys';
    }

    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        $personalAccessClient = app()->make(ClientRepository::class)->personalAccessClient();

        return $query->where('client_id', '=', $personalAccessClient?->id);
    }

    public function fields(NovaRequest $request): array
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
            })
                ->displayUsing(function ($value) {
                    return Str::limit($value, 50);
                })
                ->onlyOnIndex()
                ->readonly()
                ->copyable(),
            Code::make('API Key', function () {
                return Crypt::decryptString($this->token);
            })
                ->readonly()
                ->language('shell')
                ->help('API Keys must be passed as Bearer tokens within the Authorization header of your HTTP request.'),
            MultiSelect::make('Scopes')
                ->options(
                    Passport::scopes()
                        ->pluck('id', 'id')
                        ->sort()
                )
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
        return [];
    }
}
