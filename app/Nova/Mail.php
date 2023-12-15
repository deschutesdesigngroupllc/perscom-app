<?php

namespace App\Nova;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Mail extends Resource
{
    public static string $model = \App\Models\Mail::class;

    /**
     * @var string
     */
    public static $title = 'subject';

    /**
     * @var array
     */
    public static $search = ['id', 'subject', 'content'];

    public static function label(): string
    {
        return 'Mail';
    }

    public static function uriKey(): string
    {
        return 'mail';
    }

    public static function createButtonLabel(): string
    {
        return 'Send Mail';
    }

    public function subtitle(): ?string
    {
        return "Sent At: {$this->sent_at->toDayDateTimeString()}";
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            Text::make('Subject')
                ->rules('required')
                ->sortable(),
            Markdown::make('Content')
                ->rules('required'),
            MultiSelect::make('Recipients')
                ->options(function () {
                    if (Request::isCentralRequest()) {
                        return Tenant::all()
                            ->pluck('name', 'id')
                            ->sort();
                    }

                    return User::all()
                        ->pluck('name', 'id')
                        ->sort();
                })
                ->hideFromIndex()
                ->rules('required'),
            Boolean::make('Send Now', 'send_now')
                ->default(true)
                ->sortable()
                ->onlyOnForms(),
            DateTime::make('Send At', 'send_at')
                ->hide()
                ->dependsOn(['send_now'], function (DateTime $field, NovaRequest $request, $formData) {
                    if ($formData->send_now === false) {
                        $field->rules('required')
                            ->show();
                    }
                })
                ->onlyOnForms(),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Sent At', 'sent_at')
                ->sortable()
                ->exceptOnForms(),
            DateTime::make('Created At')
                ->exceptOnForms()
                ->sortable(),
            DateTime::make('Updated At')
                ->onlyOnDetail(),
            new Panel('Links', [
                KeyValue::make('Links', 'links')
                    ->rules('json')
                    ->keyLabel('Text')
                    ->valueLabel('URL'),
            ]),
        ];
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
