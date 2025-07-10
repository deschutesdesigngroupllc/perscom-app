<?php

declare(strict_types=1);

use App\Models\ApiLog;
use App\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function (): void {
            ApiLog::query()->select(['id', 'properties'])->chunkById(1000, function (Collection $logs): void {
                $metaInserts = [];

                /** @var ApiLog $log */
                foreach ($logs as $log) {
                    $meta = Arr::only($log->properties->toArray(), [
                        'ip',
                        'method',
                        'duration',
                        'endpoint',
                        'status',
                        'request_id',
                        'trace_id',
                    ]);

                    foreach ($meta as $key => $value) {
                        $metaInserts[] = [
                            'key' => $key,
                            'value' => $value,
                            'type' => gettype($value),
                            'status' => 1,
                            'owner_type' => ApiLog::class,
                            'owner_id' => $log->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if ($metaInserts !== []) {
                    DB::transaction(function () use ($metaInserts): void {
                        DB::table('meta')->insert($metaInserts);
                    });
                }
            });
        });
    }
};
