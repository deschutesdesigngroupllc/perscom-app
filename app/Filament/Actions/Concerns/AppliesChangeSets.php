<?php

declare(strict_types=1);

namespace App\Filament\Actions\Concerns;

use App\Actions\Copilot\ApplyChangeSet;
use App\Data\Copilot\ProposedChangeSetData;
use App\Data\Copilot\ProposedOperationData;
use Filament\Actions\BulkAction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

trait AppliesChangeSets
{
    protected function applyOperation(BulkAction $action, ProposedOperationData $operation): void
    {
        try {
            $result = ApplyChangeSet::handle(new ProposedChangeSetData([$operation]), Auth::user(), 'bulk action');
        } catch (AuthorizationException) {
            $action->failureNotificationTitle('You are not allowed to make this change. Nothing was applied.')->failure();

            return;
        } catch (ValidationException $validationException) {
            $action->failureNotificationTitle(collect($validationException->errors())->flatten()->first())->failure();

            return;
        }

        $action->successNotificationTitle(sprintf('%d record(s) were created.', $result['created']))->success();
    }
}
