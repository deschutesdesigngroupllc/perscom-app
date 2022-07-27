<?php

namespace App\Nova\Actions;

use App\Mail\NewUserLoginInformationMail;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class CreateTenantUser extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'Create New User';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            if ($model instanceof Tenant) {
                $password = $fields->password ?? Str::random();
                $user = $model->run(function () use ($fields, $password) {
                    $user = app()
                        ->make(CreatesNewUsers::class)
                        ->create([
                            'name' => $fields->name,
                            'email' => $fields->email,
                            'password' => $password,
                            'password_confirmation' => $password,
                        ]);

                    if ($fields->assign_admin_role) {
                        $user->assignRole('Admin');
                    }

                    return $user;
                });

                if ($fields->send_notification) {
                    Mail::to($user)->send(new NewUserLoginInformationMail($model, $user, $password));
                }
            }
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Name')->required(),
            Email::make('Email')->required(),
            Text::make('Password')
                ->withMeta([
                    'extraAttributes' => [
                        'type' => 'password',
                    ],
                ])
                ->help('The password will be auto-generated if left blank.'),
            Boolean::make('Assign Admin Role'),
            Boolean::make('Send Notification')->help('Send an email to the user with their login information.'),
        ];
    }
}
