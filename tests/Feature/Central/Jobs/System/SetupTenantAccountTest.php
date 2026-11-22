<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Jobs\System;

use App\Actions\Tenant\SetupTenantAccount as SetupTenantAccountAction;
use App\Jobs\System\SetupTenantAccount;
use App\Filament\Admin\Resources\TenantResource\Pages\ListTenants;
use App\Mail\System\TenantSetupDelayedMail;
use App\Models\Admin;
use App\Models\Tenant;
use App\Notifications\Admin\TenantSetupFailed;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use RuntimeException;
use Mockery;
use Mockery\MockInterface;
use Tests\Feature\Central\CentralTestCase;

class SetupTenantAccountTest extends CentralTestCase
{
    public function test_it_delegates_to_the_setup_tenant_account_action(): void
    {
        $tenant = Tenant::factory()->createQuietly();

        $captured = null;

        $this->mock(
            SetupTenantAccountAction::class,
            function (MockInterface $mock) use (&$captured): void {
                $mock->shouldReceive('handle')
                    ->once()
                    ->with(Mockery::on(function (Tenant $argument) use (&$captured): bool {
                        $captured = $argument;

                        return true;
                    }));
            }
        );

        new SetupTenantAccount($tenant)->handle();

        $this->assertNotNull($captured);
        $this->assertSame($tenant->getKey(), $captured->getKey());
    }

    public function test_a_failed_setup_alerts_admins_and_tells_the_founder(): void
    {
        Notification::fake();
        Mail::fake();

        $admin = Admin::factory()->createQuietly();
        $tenant = Tenant::factory()->createQuietly(['name' => 'Bravo Company', 'email' => 'founder@example.test']);

        new SetupTenantAccount($tenant)->failed(new RuntimeException('Database could not be created.'));

        Notification::assertSentTo($admin, TenantSetupFailed::class);
        Mail::assertQueued(TenantSetupDelayedMail::class, fn (TenantSetupDelayedMail $mail): bool => $mail->hasTo('founder@example.test'));
    }

    public function test_admins_can_retry_an_unfinished_setup(): void
    {
        Queue::fake();
        Filament::setCurrentPanel('admin');

        $this->actingAs(Admin::factory()->createQuietly(), 'admin');

        $tenant = Tenant::factory()->createQuietly(['setup_completed_at' => null]);

        Livewire::test(ListTenants::class)
            ->callAction(TestAction::make('retrySetup')->table($tenant))
            ->assertHasNoActionErrors();

        Queue::assertPushed(SetupTenantAccount::class);
    }
}
