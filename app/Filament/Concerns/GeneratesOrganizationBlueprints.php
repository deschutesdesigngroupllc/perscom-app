<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Data\Organization\OrganizationBlueprintData;
use App\Exceptions\AiUsageLimitExceededException;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Throwable;

trait GeneratesOrganizationBlueprints
{
    /**
     * Run a blueprint generation, turning any failure into a friendly notification.
     *
     * @param  Closure(): OrganizationBlueprintData  $callback
     */
    protected function generateBlueprint(Closure $callback): ?OrganizationBlueprintData
    {
        try {
            return $callback();
        } catch (AiUsageLimitExceededException $exception) {
            Notification::make()
                ->warning()
                ->title('AI Limit Reached')
                ->body($exception->getMessage())
                ->persistent()
                ->send();
        } catch (ValidationException) {
            Notification::make()
                ->danger()
                ->title('Unable to Build the Organization')
                ->body('The AI returned a structure we could not use. Please try again or adjust your description.')
                ->send();
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->danger()
                ->title('AI Service Unavailable')
                ->body('We could not reach the AI service. Please try again in a few minutes, or start from a preset instead.')
                ->send();
        }

        return null;
    }
}
