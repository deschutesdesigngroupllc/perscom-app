<?php

namespace App\Nova;

use App\Models\Enums\TaskAssignmentStatus;
use App\Nova\Actions\MarkTaskComplete;
use App\Nova\Lenses\MyTasks;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasManyThrough;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class TaskAssignment extends Resource
{
    public static string $model = \App\Models\TaskAssignment::class;

    public static array $orderBy = ['assigned_at' => 'desc'];

    /**
     * @var string
     */
    public static $title = 'id';

    /**
     * @var array
     */
    public static $search = ['id'];

    public function title(): ?string
    {
        return $this->id.optional($this->task, static function ($task) {
            return " - $task->title";
        });
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->sortable(),
            BelongsTo::make('Task')
                ->sortable(),
            BelongsTo::make('User')
                ->sortable(),
            BelongsTo::make('Assigned By', 'assigned_by', User::class)
                ->default(function (NovaRequest $request) {
                    return $request->user()
                        ->getAuthIdentifier();
                }),
            Text::make('Description', function () {
                return Str::limit($this->task?->description);
            }),
            Badge::make('Status', function () {
                return $this->status?->value;
            })
                ->map([
                    TaskAssignmentStatus::TASK_ASSIGNED->value => 'info',
                    TaskAssignmentStatus::TASK_COMPLETE->value => 'success',
                    TaskAssignmentStatus::TASK_COMPLETE_EXPIRED->value => 'warning',
                    TaskAssignmentStatus::TASK_COMPLETE_PAST_DUE->value => 'warning',
                    TaskAssignmentStatus::TASK_EXPIRED->value => 'danger',
                    TaskAssignmentStatus::TASK_PASTDUE->value => 'danger',
                ])
                ->withIcons()
                ->label(function ($value) {
                    return Str::replace('_', ' ', $value);
                }),
            new Panel('Important Dates', [
                DateTime::make('Assigned At')
                    ->default(now())
                    ->sortable(),
                DateTime::make('Due At')
                    ->sortable(),
                DateTime::make('Completed At')
                    ->sortable(),
                DateTime::make('Expires At')
                    ->sortable(),
            ]),
            URL::make('Form', function () {
                return $this->task?->form?->url;
            })
                ->displayUsing(function ($url) {
                    return 'Click To Open Form';
                })
                ->canSee(function () {
                    return $this->task?->form;
                })
                ->onlyOnDetail(),
            new Panel('Details', [
                Markdown::make('Instructions', function () {
                    return $this->task?->instructions;
                })
                    ->alwaysShow(),
            ]),
            HasManyThrough::make('Attachments'),
        ];
    }

    public function authorizedToRunAction(NovaRequest $request, Action $action): bool
    {
        return true;
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
        return [
            new MyTasks(),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            (new MarkTaskComplete())->showInline(),
        ];
    }
}
