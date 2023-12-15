<?php

namespace App\Nova;

use App\Features\ExportDataFeature;
use App\Nova\Filters\Status as StatusFilter;
use App\Traits\HasFields;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Traits\HasActionsInTabs;
use Eminiarts\Tabs\Traits\HasTabs;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Pennant\Feature;

class Submission extends Resource
{
    use HasActionsInTabs;
    use HasFields;
    use HasTabs;

    public static string $model = \App\Models\Submission::class;

    public static array $orderBy = ['created_at' => 'desc'];

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
    public static $search = ['id'];

    public function title(): ?string
    {
        return $this->id.optional($this->form, static function ($form) {
            return " - $form->name";
        });
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            BelongsTo::make('Form')
                ->showOnPreview()
                ->default(function (NovaRequest $request) {
                    return $request->viaResource === Form::uriKey() ? $request->viaResourceId : null;
                }),
            BelongsTo::make('User')
                ->showOnPreview()
                ->default(function (NovaRequest $request) {
                    return $request->user()->id;
                }),
            $this->generateBadgeField($this),
            Heading::make('Meta')
                ->onlyOnDetail(),
            DateTime::make('Created At')
                ->exceptOnForms()
                ->sortable(),
            DateTime::make('Updated At')
                ->exceptOnForms()
                ->sortable(),
            $this->getNovaFields($request, true, function ($form) {
                return $form?->name;
            }, function ($model) {
                return $model?->form;
            }),
            Tabs::make('Settings', [
                Tab::make('Status History', [
                    MorphToMany::make('Status', 'statuses', Status::class)
                        ->allowDuplicateRelations()
                        ->showCreateRelationButton()
                        ->fields(function () {
                            return [
                                Textarea::make('Text'),
                                Text::make('Text', function ($model) {
                                    return $model->text;
                                }),
                                DateTime::make('Updated At')
                                    ->sortable()
                                    ->onlyOnIndex(),
                            ];
                        }),
                ]),
                Tab::make('Logs', [$this->actionfield()]),
            ])
                ->showTitle(),
        ];
    }

    protected function generateBadgeField($submission): Badge
    {
        $status = $submission->statuses()
            ->first();

        $badge = Badge::make('Status', static function () use ($status) {
            return $status->name ?? 'No Current Status';
        })
            ->types([
                'No Current Status' => 'bg-gray-100 text-gray-600',
            ])
            ->label(function ($status) {
                return $status ?? 'No Current Status';
            })
            ->showOnPreview();

        if ($status) {
            $badge->addTypes([
                $status->name => $status->color,
            ]);
        }

        return $badge;
    }

    protected function getCustomFields(NovaRequest $request): Panel
    {
        $form = $this->getForm($request);

        $fields = [];
        if ($form) {
            foreach ($form->fields as $field) {
                $fields[] = $field->constructNovaField();
            }
        }

        return new Panel($form->name ?? 'Form', $fields);
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [new StatusFilter()];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return [ExportAsCsv::make('Export '.self::label())
            ->canSee(function () {
                return Feature::active(ExportDataFeature::class);
            })
            ->nameable()];
    }
}
