<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources\PassportTokenResource\Widgets;

use App\Filament\App\Resources\PassportTokenResource\Widgets\ApiRequests;
use App\Models\ApiLog;
use ReflectionMethod;
use Tests\Feature\Tenant\TenantTestCase;

class ApiRequestsTest extends TenantTestCase
{
    public function test_it_charts_the_trailing_twelve_months_ending_this_month(): void
    {
        ApiLog::query()->insert([
            ['log_name' => 'api', 'description' => 'GET /users', 'created_at' => now(), 'updated_at' => now()],
            ['log_name' => 'api', 'description' => 'GET /users', 'created_at' => now()->subMonths(11), 'updated_at' => now()],
            ['log_name' => 'api', 'description' => 'GET /users', 'created_at' => now()->subMonths(13), 'updated_at' => now()],
        ]);

        $data = new ReflectionMethod(ApiRequests::class, 'getData')->invoke(new ApiRequests);

        $this->assertCount(12, $data['labels']);
        $this->assertSame(now()->format('Y-m'), $data['labels']->last());
        $this->assertSame(now()->subMonths(11)->format('Y-m'), $data['labels']->first());
        $this->assertSame(2, (int) $data['datasets'][0]['data']->sum());
    }
}
