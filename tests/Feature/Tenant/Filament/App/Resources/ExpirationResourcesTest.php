<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\AutomationResource\Pages\EditAutomation;
use App\Filament\App\Resources\CredentialResource\Pages\EditCredential;
use App\Filament\App\Resources\TrainingRecordResource\Pages\ListTrainingRecords;
use App\Filament\Tables\Columns\ExpiresAtColumn;
use App\Models\Automation;
use App\Models\Credential;
use App\Models\Enums\AutomationRunLimit;
use App\Models\Enums\AutomationTrigger;
use App\Models\Issuer;
use App\Models\TrainingRecord;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class ExpirationResourcesTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);
    }

    public function test_credential_validity_period_can_be_saved(): void
    {
        $credential = Credential::factory()->for(Issuer::factory())->createQuietly();

        Livewire::test(EditCredential::class, ['record' => $credential->getKey()])
            ->fillForm(['validity_days' => 365])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertSame(365, $credential->refresh()->validity_days);
    }

    public function test_automation_run_limit_can_be_saved(): void
    {
        $automation = Automation::factory()->messageAction()->forTrigger(AutomationTrigger::USER_DAILY_REVIEW)->createQuietly();

        Livewire::test(EditAutomation::class, ['record' => $automation->getKey()])
            ->fillForm(['run_limit' => AutomationRunLimit::ONCE_PER_DAY->value])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(AutomationRunLimit::ONCE_PER_DAY, $automation->refresh()->run_limit);
    }

    public function test_training_records_list_shows_credential_expiration(): void
    {
        $credential = Credential::factory()->createQuietly(['name' => 'Combat Lifesaver']);
        $trainingRecord = TrainingRecord::factory()->createQuietly();
        $trainingRecord->credentials()->attach($credential, ['expires_at' => now()->subDay()]);

        Livewire::test(ListTrainingRecords::class)
            ->assertCanSeeTableRecords([$trainingRecord])
            ->assertSee('Combat Lifesaver (Expired');
    }

    public function test_expiration_colors(): void
    {
        $this->assertSame('gray', ExpiresAtColumn::colorFor(null));
        $this->assertSame('danger', ExpiresAtColumn::colorFor(now()->subMinute()));
        $this->assertSame('warning', ExpiresAtColumn::colorFor(now()->addDays(10)));
        $this->assertSame('success', ExpiresAtColumn::colorFor(now()->addDays(90)));
    }
}
