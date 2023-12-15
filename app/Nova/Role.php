<?php

namespace App\Nova;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphedByMany;
use Laravel\Nova\Fields\Tag;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\Permission\PermissionRegistrar;

class Role extends Resource
{
    public static string $model = \App\Models\Role::class;

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
            Boolean::make('Custom Role', function ($role) {
                return $role->is_custom_role;
            }),
            Boolean::make('Application Role', function ($role) {
                return $role->is_application_role;
            }),
            Tag::make('Permissions')
                ->showCreateRelationButton(),
            MorphedByMany::make('Users'),
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
