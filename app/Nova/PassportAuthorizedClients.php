<?php

namespace App\Nova;

use App\Features\OAuth2AccessFeature;
use App\Models\PassportToken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Laravel\Pennant\Feature;

class PassportAuthorizedClients extends Resource
{
    public static string $model = PassportToken::class;

    /**
     * @var array
     */
    public static $search = ['id', 'name'];

    public static function uriKey(): string
    {
        return 'authorized-clients';
    }

    public static function label(): string
    {
        return 'Authorized Clients';
    }

    public function title(): ?string
    {
        return $this->client?->name ?? 'Client';
    }

    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        $personalAccessClient = app()->make(ClientRepository::class)->personalAccessClient();

        return $query->where('client_id', '<>', $personalAccessClient?->id);
    }

    public function fields(NovaRequest $request): array
    {
        return [
            BelongsTo::make('Application', 'client', PassportClient::class)
                ->sortable()
                ->readonly(),
            MultiSelect::make('Scopes')
                ->options(
                    Passport::scopes()
                        ->pluck('id', 'id')
                        ->sort()
                )
                ->hideFromIndex()
                ->readonly(),
            Boolean::make('Revoked')
                ->default(false)
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

    public static function authorizedToViewAny(Request $request): bool
    {
        return Feature::active(OAuth2AccessFeature::class) && Gate::check('viewAny', PassportToken::class);
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }

    public static function redirectAfterCreate(NovaRequest $request, $resource): string
    {
        return '/resources/'.static::uriKey();
    }

    public static function afterCreate(NovaRequest $request, Model $model): void
    {
        $request->user()
            ->createToken($model->name, $model->scopes);
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
