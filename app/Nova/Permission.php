<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\Permission\PermissionRegistrar;

class Permission extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Permission::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id', 'name'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')
                ->sortable()
                ->rules(['required']),
            Textarea::make('Description')
                ->nullable()
                ->alwaysShow()
                ->showOnPreview(),
            Text::make('Description', function ($model) {
                return $model->description;
            })->onlyOnIndex(),
            Boolean::make('Custom Permission', function ($permission) {
                return !collect(config('permissions.permissions'))->has(
                    $permission->name
                );
            }),
            Boolean::make('Application Permission', function ($permission) {
                return collect(config('permissions.permissions'))->has(
                    $permission->name
                );
            }),
            BelongsToMany::make('Roles')->showCreateRelationButton(),
            //	        Panel::make('Organization', [
            //		        BooleanGroup::make('Awards')->options([
            //			        'create' => 'Create',
            //			        'read' => 'Read',
            //			        'update' => 'Update',
            //			        'delete' => 'Delete',
            //		        ]),
            //		        BooleanGroup::make('Documents')->options([
            //			        'create' => 'Create',
            //			        'read' => 'Read',
            //			        'update' => 'Update',
            //			        'delete' => 'Delete',
            //		        ]),
            //		        BooleanGroup::make('Permissions')->options([
            //			        'create' => 'Create',
            //			        'read' => 'Read',
            //			        'update' => 'Update',
            //			        'delete' => 'Delete',
            //		        ]),
            //		        BooleanGroup::make('Positions')->options([
            //			        'create' => 'Create',
            //			        'read' => 'Read',
            //			        'update' => 'Update',
            //			        'delete' => 'Delete',
            //		        ]),
            //		        BooleanGroup::make('Qualifications')->options([
            //			        'create' => 'Create',
            //			        'read' => 'Read',
            //			        'update' => 'Update',
            //			        'delete' => 'Delete',
            //		        ]),
            //		        BooleanGroup::make('Specialties')->options([
            //			        'create' => 'Create',
            //			        'read' => 'Read',
            //			        'update' => 'Update',
            //			        'delete' => 'Delete',
            //		        ]),
            //		        BooleanGroup::make('Ranks')->options([
            //			        'create' => 'Create',
            //			        'read' => 'Read',
            //			        'update' => 'Update',
            //			        'delete' => 'Delete',
            //		        ]),
            //		        BooleanGroup::make('Roles')->options([
            //			        'create' => 'Create',
            //			        'read' => 'Read',
            //			        'update' => 'Update',
            //			        'delete' => 'Delete',
            //		        ]),
            //		        BooleanGroup::make('Units')->options([
            //			        'create' => 'Create',
            //			        'read' => 'Read',
            //			        'update' => 'Update',
            //			        'delete' => 'Delete',
            //		        ]),
            //		        BooleanGroup::make('Users')->options([
            //			        'create' => 'Create',
            //			        'read' => 'Read',
            //			        'update' => 'Update',
            //			        'delete' => 'Delete',
            //		        ]),
            //	        ])->collapsable(),
        ];
    }

    /**
     * Register a callback to be called after the resource is created.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public static function afterCreate(NovaRequest $request, Model $model)
    {
        // Reset permission cache
        app()
            ->make(PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }

    /**
     * Register a callback to be called after the resource is updated.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public static function afterUpdate(NovaRequest $request, Model $model)
    {
        // Reset permission cache
        app()
            ->make(PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }

    /**
     * Register a callback to be called after the resource is deleted.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public static function afterDelete(NovaRequest $request, Model $model)
    {
        // Reset permission cache
        app()
            ->make(PermissionRegistrar::class)
            ->forgetCachedPermissions();
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
