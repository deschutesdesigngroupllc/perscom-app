<?php

use App\Models\Field;
use App\Models\Tenant;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    /**
     * Process the operation.
     */
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function ($tenant) {
            $field = Field::where('type', 'datetime')->get()->each(function (Field $field) {
                $field->forceFill([
                    'type' => 'datetime-local',
                ])->saveQuietly();
            });

        });
    }
};
