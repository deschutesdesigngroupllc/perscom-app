<?php

namespace App\Nova;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Tag;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\Permission\PermissionRegistrar;

class Permission extends Resource
{
    public static string $model = \App\Models\Permission::class;

    public static array $orderBy = ['name' => 'asc'];

    /**
     * @var string
     */
    public static $title = 'name';

    /**
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * @var array
     */
    public static $search = ['id', 'name'];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->hideFromIndex(),
            Text::make('Name')
                ->sortable()
                ->rules(['required']),
            Textarea::make('Description')
                ->nullable()
                ->alwaysShow()
                ->showOnPreview(),
            Text::make('Description', function ($model) {
                return $model->description;
            })
                ->onlyOnIndex(),
            Boolean::make('Custom Permission', function ($permission) {
                return $permission->is_custom_permission;
            }),
            Boolean::make('Application Permission', function ($permission) {
                return $permission->is_application_permission;
            }),
            Tag::make('Roles')
                ->showCreateRelationButton(),
        ];
    }

    /**
     * @throws BindingResolutionException
     */
    public static function afterCreate(NovaRequest $request, Model $model): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @throws BindingResolutionException
     */
    public static function afterUpdate(NovaRequest $request, Model $model): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @throws BindingResolutionException
     */
    public static function afterDelete(NovaRequest $request, Model $model): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
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
