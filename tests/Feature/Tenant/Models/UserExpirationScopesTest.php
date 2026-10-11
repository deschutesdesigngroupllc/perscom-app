<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Models;

use App\Models\Credential;
use App\Models\QualificationRecord;
use App\Models\TrainingRecord;
use App\Models\User;
use Tests\Feature\Tenant\TenantTestCase;

class UserExpirationScopesTest extends TenantTestCase
{
    public function test_where_has_expiring_credentials_scope(): void
    {
        $expiring = $this->userWithCredentialExpiringAt(now()->addDays(10));
        $later = $this->userWithCredentialExpiringAt(now()->addDays(60));
        $expired = $this->userWithCredentialExpiringAt(now()->subDay());

        $ids = User::query()->whereHasExpiringCredentials(30)->pluck('id');

        $this->assertContains($expiring->getKey(), $ids);
        $this->assertNotContains($later->getKey(), $ids);
        $this->assertNotContains($expired->getKey(), $ids);
    }

    public function test_where_has_expired_credentials_scope(): void
    {
        $expired = $this->userWithCredentialExpiringAt(now()->subDay());
        $valid = $this->userWithCredentialExpiringAt(now()->addDays(10));

        $ids = User::query()->whereHasExpiredCredentials()->pluck('id');

        $this->assertContains($expired->getKey(), $ids);
        $this->assertNotContains($valid->getKey(), $ids);
    }

    public function test_where_has_expiring_qualifications_scope(): void
    {
        $expiring = $this->userWithQualificationExpiringAt(now()->addDays(5));
        $never = $this->userWithQualificationExpiringAt(null);

        $ids = User::query()->whereHasExpiringQualifications(30)->pluck('id');

        $this->assertContains($expiring->getKey(), $ids);
        $this->assertNotContains($never->getKey(), $ids);
    }

    public function test_where_has_expired_qualifications_scope(): void
    {
        $expired = $this->userWithQualificationExpiringAt(now()->subDays(2));
        $valid = $this->userWithQualificationExpiringAt(now()->addDays(2));

        $ids = User::query()->whereHasExpiredQualifications()->pluck('id');

        $this->assertContains($expired->getKey(), $ids);
        $this->assertNotContains($valid->getKey(), $ids);
    }

    private function userWithCredentialExpiringAt(mixed $expiresAt): User
    {
        $user = User::factory()->createQuietly();
        $trainingRecord = TrainingRecord::factory()->for($user)->createQuietly();
        $trainingRecord->credentials()->attach(Credential::factory()->createQuietly(), ['expires_at' => $expiresAt]);

        return $user;
    }

    private function userWithQualificationExpiringAt(mixed $expiresAt): User
    {
        $user = User::factory()->createQuietly();
        QualificationRecord::factory()->for($user)->createQuietly(['expires_at' => $expiresAt]);

        return $user;
    }
}
