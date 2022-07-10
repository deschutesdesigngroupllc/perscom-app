<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Tenant extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Tenant::class;

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
    public static $search = ['id', 'name', 'website'];

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
                ->rules(['required', Rule::unique('tenants', 'name')->ignore($this->id)]),
            Email::make('Email')
                ->sortable()
                ->rules(['required', Rule::unique('tenants', 'email')->ignore($this->id)]),
            Text::make('Website')->sortable(),
            URL::make('Domain', function ($model) {
                if ($model->domains()->count()) {
                    return \Spatie\Url\Url::fromString($model->domains()->first()->domain)
                        ->withScheme(app()->environment() === 'production' ? 'https' : 'http')
                        ->__toString();
                }
                return null;
            })
                ->sortable()
                ->displayUsing(function ($url) {
                    return $url;
                })
                ->exceptOnForms(),
            new Panel('Domain', [
                Text::make('Domain', 'domain')
                    ->rules(['required'])
                    ->onlyOnForms()
                    ->hideWhenUpdating()
                    ->fillUsing(function ($request) {
                        return null;
                    }),
            ]),
            new Panel('Administrative User', [
                Text::make('Name', 'admin_name')
                    ->rules(['required'])
                    ->onlyOnForms()
                    ->hideWhenUpdating()
                    ->fillUsing(function () {
                        return null;
                    }),
                Email::make('Email', 'admin_email')
                    ->rules(['required'])
                    ->onlyOnForms()
                    ->hideWhenUpdating()
                    ->fillUsing(function () {
                        return null;
                    }),
                Password::make('Password', 'admin_password')
                    ->rules(['required'])
                    ->onlyOnForms()
                    ->hideWhenUpdating()
                    ->fillUsing(function () {
                        return null;
                    }),
            ]),
            HasMany::make('Domains'),
            Heading::make('Meta')->onlyOnDetail(),
            DateTime::make('Created At')
                ->sortable()
                ->exceptOnForms(),
            DateTime::make('Updated At')
                ->sortable()
                ->exceptOnForms()
                ->onlyOnDetail(),
            new Panel('Subscription', [
                Boolean::make('Customer', function ($model) {
                    return $model->hasStripeId();
                }),
                Boolean::make('On Trial', function ($model) {
                    return $model->onGenericTrial();
                }),
                Text::make('Stripe ID')
                    ->onlyOnDetail()
                    ->readonly(),
                Text::make('Card Brand')
                    ->onlyOnDetail()
                    ->readonly(),
                Text::make('Card Last Four')
                    ->onlyOnDetail()
                    ->readonly(),
                DateTime::make('Trial Ends At')
                    ->onlyOnDetail()
                    ->readonly(),
            ]),
            HasMany::make('Subscriptions'),
            HasMany::make('Receipts', 'localReceipts', Receipt::class),
	        new Panel('Database', [
		        Text::make('Database Name', function ($model) {
		        	return $model->tenancy_db_name;
		        })->readonly()->onlyOnDetail(),
		        Boolean::make('Database Created', function ($model) {
			        return $model->run(function () {
			        	return Schema::hasTable('users');
			        });
		        }),
	        ]),
        ];
    }

    /**
     * Register a callback to be called after the resource is created.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
//    public static function afterCreate(NovaRequest $request, Model $model)
//    {
//        if ($model instanceof \App\Models\Tenant) {
//            $values = $request->all();
//
//            $model->domains()->create([
//                'domain' => $values['domain'],
//            ]);
//
//            $model->run(function () use ($values) {
//                $user = \App\Models\User::create([
//                    'name' => $values['admin_name'],
//                    'email' => $values['admin_email'],
//                    'password' => Hash::make($values['admin_password']),
//                ]);
//                $user->assignRole('Admin');
//            });
//        }
//    }

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
