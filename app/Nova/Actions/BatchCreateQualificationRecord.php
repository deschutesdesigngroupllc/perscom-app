<?php

namespace App\Nova\Actions;

use App\Models\User;
use App\Nova\Qualification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class BatchCreateQualificationRecord extends Action
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
    public $name = 'Batch Create Qualification Record';

    /**
     * @var string
     */
    public $confirmText = 'Select the users to create an qualification record.';

    /**
     * @var string
     */
    public $confirmButtonText = 'Create Qualification Records';

    /**
     * @var string
     */
    public $modalSize = '5xl';

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        foreach ($fields->users ?? [] as $userId) {
            $user = User::findOrFail($userId);
            $user->qualification_records()
                ->create([
                    'qualification_id' => $fields->qualification->id ?? null,
                    'text' => $fields->text ?? null,
                    'document_id' => $fields->document->id ?? null,
                ]);
        }

        return Action::message('You have successfully created the qualification records.');
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
            BelongsTo::make(Str::singular(Str::title(setting('localization_qualifications', 'Qualification'))), 'qualification', Qualification::class)
                ->showCreateRelationButton(),
            Textarea::make('Text'),
            BelongsTo::make('Document')
                ->nullable(),
        ];
    }
}
