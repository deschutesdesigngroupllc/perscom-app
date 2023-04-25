<?php

namespace App\Nova\Actions;

use App\Models\User;
use App\Nova\Position;
use App\Nova\Specialty;
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

            $user->assignment_records()->create([
                'position_id' => $fields->position?->id,
                'specialty_id' => $fields->specialty?->id,
                'unit_id' => $fields->unit?->id,
                'text' => $fields->text,
                'document_id' => $fields->document?->id,
            ]);
        }

        return Action::message('You have successfully created the assignment records.');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            MultiSelect::make(Str::plural(Str::title(setting('localization_users', 'Users'))), 'users')->options(
                User::all()->mapWithKeys(fn ($user) => [$user->id => $user->name])->sort()
            )->rules('required'),
            BelongsTo::make(Str::singular(Str::title(setting('localization_positions', 'Position'))), 'position', Position::class)->showCreateRelationButton(),
            BelongsTo::make(Str::singular(Str::title(setting('localization_specialties', 'Specialty'))), 'specialty', Specialty::class)->showCreateRelationButton(),
            BelongsTo::make(Str::singular(Str::title(setting('localization_units', 'Unit'))), 'unit', Unit::class)->showCreateRelationButton(),
            Textarea::make('Text'),
            BelongsTo::make('Document')->nullable(),
        ];
    }
}
