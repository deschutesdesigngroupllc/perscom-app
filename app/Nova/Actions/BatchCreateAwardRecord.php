<?php

namespace App\Nova\Actions;

use App\Models\User;
use App\Nova\Award;
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

class BatchCreateAwardRecord extends Action
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
    public $name = 'Batch Create Award Record';

    /**
     * @var string
     */
    public $confirmText = 'Select the users to create an award record.';

    /**
     * @var string
     */
    public $confirmButtonText = 'Create Award Records';

    /**
     * @var string
     */
    public $modalSize = '5xl';

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        foreach ($fields->users ?? [] as $userId) {
            $user = User::findOrFail($userId);
            $user->award_records()->create([
                'award_id' => $fields->award->id ?? null,
                'text' => $fields->text ?? null,
                'document_id' => $fields->document->id ?? null,
            ]);
        }

        return Action::message('You have successfully created the award records.');
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
            BelongsTo::make(Str::singular(Str::title(setting('localization_awards', 'Award'))), 'award', Award::class)
                ->showCreateRelationButton(),
            Textarea::make('Text'),
            BelongsTo::make('Document')
                ->nullable(),
        ];
    }
}
