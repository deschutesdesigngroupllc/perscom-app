<?php

declare(strict_types=1);

use App\Models\ApiLog;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        Model::unsetEventDispatcher();

        tenancy()->runForMultiple(Tenant::all(), function (): void {
            $min = ApiLog::query()->min('id');

            if (blank($min)) {
                return;
            }

            DB::query()
                ->from('meta')
                ->where('owner_type', ApiLog::class)
                ->where('owner_id', '<', $min)
                ->delete();
        });
    }
};
