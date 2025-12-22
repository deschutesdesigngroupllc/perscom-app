<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

/**
 * @mixin CreateRecord
 */
trait SetsUserFromRequestQuery
{
    public function booted(): void
    {
        $livewire = $this->form->getLivewire();
        $statePath = $this->form->getStatePath();
        data_set($livewire, $statePath.'.user_id', Arr::wrap(request()->query('user_id')));
    }
}
