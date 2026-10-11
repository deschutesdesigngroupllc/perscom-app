<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Models\Credential;
use App\Models\TrainingRecord;
use App\Models\TrainingRecordCredential;
use Tests\Feature\Tenant\TenantTestCase;

class TrainingRecordCredentialObserverTest extends TenantTestCase
{
    public function test_expiration_is_calculated_from_the_credential_validity_period(): void
    {
        $this->freezeSecond();

        $credential = Credential::factory()->createQuietly(['validity_days' => 90]);
        $trainingRecord = TrainingRecord::factory()->createQuietly(['created_at' => now()->subDays(10)]);

        $trainingRecord->credentials()->attach($credential);

        $pivot = TrainingRecordCredential::query()->firstOrFail();

        $this->assertTrue($pivot->issued_at->equalTo(now()->subDays(10)));
        $this->assertTrue($pivot->expires_at->equalTo(now()->subDays(10)->addDays(90)));
    }

    public function test_credentials_without_a_validity_period_never_expire(): void
    {
        $credential = Credential::factory()->createQuietly(['validity_days' => null]);
        $trainingRecord = TrainingRecord::factory()->createQuietly();

        $trainingRecord->credentials()->attach($credential);

        $pivot = TrainingRecordCredential::query()->firstOrFail();

        $this->assertNotNull($pivot->issued_at);
        $this->assertNull($pivot->expires_at);
    }

    public function test_an_explicit_expiration_is_not_overwritten(): void
    {
        $this->freezeSecond();

        $credential = Credential::factory()->createQuietly(['validity_days' => 90]);
        $trainingRecord = TrainingRecord::factory()->createQuietly();

        $trainingRecord->credentials()->attach($credential, ['expires_at' => now()->addDays(5)]);

        $this->assertTrue(TrainingRecordCredential::query()->firstOrFail()->expires_at->equalTo(now()->addDays(5)));
    }

    public function test_syncing_credentials_calculates_expiration(): void
    {
        $credential = Credential::factory()->createQuietly(['validity_days' => 30]);
        $trainingRecord = TrainingRecord::factory()->createQuietly();

        $trainingRecord->credentials()->sync([$credential->getKey()]);

        $this->assertNotNull($trainingRecord->credentials()->firstOrFail()->pivot->expires_at);
    }
}
