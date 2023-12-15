<?php

namespace App\Nova;

use App\Nova\Actions\DownloadReceipt;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Receipt extends Resource
{
    public static string $model = \Spark\Receipt::class;

    public static array $orderBy = ['paid_at' => 'desc'];

    /**
     * @var string
     */
    public static $title = 'id';

    /**
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * @var array
     */
    public static $search = ['id', 'amount'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            BelongsTo::make('Tenant', 'owner')
                ->showCreateRelationButton()
                ->sortable(),
            Text::make('Amount')
                ->readonly()
                ->sortable(),
            Text::make('Tax')
                ->readonly()
                ->sortable(),
            Text::make('Paid At')
                ->readonly()
                ->sortable(),
        ];
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }

    public function authorizedToReplicate(Request $request): bool
    {
        return false;
    }

    public function authorizedToUpdate(Request $request): bool
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
        return [new DownloadReceipt()];
    }
}
