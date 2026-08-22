<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Actions\Tenant;

use App\Actions\Tenant\RemoveTenantAccount;
use App\Models\Tenant;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Central\CentralTestCase;

class RemoveTenantAccountTest extends CentralTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        Storage::fake('s3');

        // Stop the tenancy filesystem bootstrapper from swapping the faked 's3'
        // disk for a real (network) one when tenancy initializes inside run().
        config(['tenancy.filesystem.disks' => []]);
    }

    public function test_deletes_tenant_files_by_default(): void
    {
        $tenant = Tenant::factory()->createQuietly();

        $tenant->run(fn () => Storage::disk('s3')->put('documents/report.pdf', 'contents'));
        Storage::disk('s3')->assertExists('documents/report.pdf');

        (new RemoveTenantAccount)->handle($tenant);

        Storage::disk('s3')->assertMissing('documents/report.pdf');
    }

    public function test_retains_files_when_file_deletion_is_disabled(): void
    {
        $tenant = Tenant::factory()->createQuietly();

        $tenant->run(fn () => Storage::disk('s3')->put('documents/report.pdf', 'contents'));

        (new RemoveTenantAccount)
            ->shouldDeleteFiles(false)
            ->handle($tenant);

        Storage::disk('s3')->assertExists('documents/report.pdf');
    }

    public function test_should_delete_files_returns_the_same_instance(): void
    {
        $action = new RemoveTenantAccount;

        $this->assertSame($action, $action->shouldDeleteFiles(false));
    }
}
