<?php

namespace App\Nova\Actions;

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
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MultiSelect;
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

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($fields->users as $userId) {
            $user = User::findOrFail($userId);

            $user->assignment_records()
                ->create([
                    'status_id' => $fields->status?->id,
                    'unit_id' => $fields->unit?->id,
                    'secondary_unit_ids' => $fields->secondary_units,
                    'position_id' => $fields->position?->id,
                    'secondary_position_ids' => $fields->secondary_positions,
                    'specialty_id' => $fields->specialty?->id,
                    'secondary_specialty_ids' => $fields->secondary_specialties,
                    'text' => $fields->text,
                    'document_id' => $fields->document?->id,
                ]);
        }

        return Action::message('You have successfully created the assignment records.');
    }

    /**
     * Get the fields available on the action.
     */
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
            BelongsTo::make(Str::singular(Str::title(setting('localization_statuses', 'Status'))), 'status', Status::class)
                ->required(false)
                ->nullable()
                ->showCreateRelationButton(),
            BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_positions', 'Position'))), 'position', Position::class)
                ->showCreateRelationButton(),
            MultiSelect::make('Secondary '.Str::plural(Str::title(setting('localization_positions', 'Positions'))), 'secondary_positions')
                ->options(
                    \App\Models\Position::all()
                        ->pluck('name', 'id')
                        ->sort()
                )
                ->hideFromIndex(),
            BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_specialties', 'Specialty'))), 'specialty', Specialty::class)
                ->showCreateRelationButton(),
            MultiSelect::make('Secondary '.Str::plural(Str::title(setting('localization_specialties', 'Specialties'))), 'secondary_specialties')
                ->options(
                    \App\Models\Specialty::all()
                        ->pluck('name', 'id')
                        ->sort()
                )
                ->hideFromIndex(),
            BelongsTo::make('Primary '.Str::singular(Str::title(setting('localization_units', 'Unit'))), 'unit', Unit::class)
                ->showCreateRelationButton(),
            MultiSelect::make('Secondary '.Str::plural(Str::title(setting('localization_units', 'Units'))), 'secondary_units')
                ->options(
                    \App\Models\Unit::all()
                        ->pluck('name', 'id')
                        ->sort()
                )
                ->hideFromIndex(),
            Textarea::make('Text'),
            BelongsTo::make('Document')
                ->nullable(),
        ];
    }
}
