<?php

namespace App\Nova\Actions;

use App\Models\Enums\AssignmentRecordType;
use App\Models\User;
use App\Nova\Position;
use App\Nova\Specialty;
use App\Nova\Status;
use App\Nova\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class BatchCreateAssignmentRecord extends Action
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * @var bool
     */
    public $standalone = true;

    /**
     * @var string
     */
    public $name = 'Batch Create Assignment Record';

    /**
     * @var string
     */
    public $confirmText = 'Select the users to create an assignment record.';

    /**
     * @var string
     */
    public $confirmButtonText = 'Create Assignment Records';

    /**
     * @var string
     */
    public $modalSize = '5xl';

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        foreach ($fields->users ?? [] as $userId) {
            $user = User::findOrFail($userId);
            $user->assignment_records()->create([
                'status_id' => $fields->status->id ?? null,
                'unit_id' => $fields->unit->id ?? null,
                'position_id' => $fields->position->id ?? null,
                'specialty_id' => $fields->specialty->id ?? null,
                'type' => $fields->type ?? null,
                'text' => $fields->text ?? null,
                'document_id' => $fields->document->id ?? null,
            ]);
        }

        return Action::message('You have successfully created the assignment records.');
    }

    public function fields(NovaRequest $request): array
    {
        return [
            MultiSelect::make(Str::plural(Str::title(setting('localization_users', 'Users'))), 'users')
                ->options(
                    User::all()
                        ->pluck('name', 'id')
                        ->sort()
                )
                ->rules('required'),
            Select::make('Type')
                ->options(collect(AssignmentRecordType::cases())->mapWithKeys(fn (AssignmentRecordType $case) => [$case->value => $case->getLabel()]))
                ->rules('required')
                ->displayUsingLabels()
                ->default(AssignmentRecordType::PRIMARY->value),
            BelongsTo::make(Str::singular(Str::title(setting('localization_statuses', 'Status'))), 'status', Status::class)
                ->nullable()
                ->showCreateRelationButton(),
            BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_positions', 'Position'))), 'position', Position::class)
                ->nullable()
                ->showCreateRelationButton(),
            BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_specialties', 'Specialty'))), 'specialty', Specialty::class)
                ->nullable()
                ->showCreateRelationButton(),
            BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_units', 'Unit'))), 'unit', Unit::class)
                ->nullable()
                ->showCreateRelationButton(),
            Textarea::make('Text'),
            BelongsTo::make('Document')
                ->nullable(),
        ];
    }
}
