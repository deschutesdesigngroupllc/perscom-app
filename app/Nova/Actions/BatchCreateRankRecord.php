<?php

namespace App\Nova\Actions;

use App\Models\RankRecord;
use App\Models\User;
use App\Nova\Rank;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class BatchCreateRankRecord extends Action
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
    public $name = 'Batch Create Rank Record';

    /**
     * @var string
     */
    public $confirmText = 'Select the users to create an rank record.';

    /**
     * @var string
     */
    public $confirmButtonText = 'Create Rank Records';

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

            $user->rank_records()->create([
                'rank_id' => $fields->rank?->id,
                'type' => $fields->type,
                'text' => $fields->text,
                'document_id' => $fields->document?->id,
            ]);
        }

        return Action::message('You have successfully created the rank records.');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            MultiSelect::make(Str::plural(Str::title(setting('localization_users', 'Users'))), 'users')
                ->options(
                    User::all()->pluck('name', 'id')->sort()
                )
                ->rules('required'),
            BelongsTo::make(Str::singular(Str::title(setting('localization_ranks', 'Rank'))), 'rank', Rank::class)
                ->showCreateRelationButton(),
            Select::make('Type')
                ->options([
                    RankRecord::RECORD_RANK_PROMOTION => 'Promotion',
                    RankRecord::RECORD_RANK_DEMOTION => 'Demotion',
                ])
                ->rules('required')
                ->displayUsingLabels(),
            Textarea::make('Text'),
            BelongsTo::make('Document')
                ->nullable(),
        ];
    }
}
