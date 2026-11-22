<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Wizard;

/**
 * Filament renders create wizards uncontained, which leaves the step header in a card and the
 * fields and buttons floating on the page beneath it. Keep the whole wizard in one card instead.
 */
trait HasContainedWizard
{
    use HasWizard;

    public function getWizardComponent(): Component
    {
        return Wizard::make($this->getSteps())
            ->startOnStep($this->getStartStep())
            ->cancelAction($this->getCancelFormAction())
            ->submitAction($this->getSubmitFormAction())
            ->alpineSubmitHandler("\$wire.{$this->getSubmitFormLivewireMethodName()}()")
            ->skippable($this->hasSkippableSteps())
            ->contained();
    }
}
