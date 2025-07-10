<?php

declare(strict_types=1);

use App\Models\ApiLog;
use App\Models\Tenant;
use Illuminate\Support\Arr;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function (): void {
            ApiLog::query()->lazy()->each(function (ApiLog $log): void {
                $log->setMeta(Arr::only($log->properties->toArray(), [
                    'ip',
                    'method',
                    'duration',
                    'endpoint',
                    'status',
                    'request_id',
                    'trace_id',
                ]));
            });
        });
    }
};
