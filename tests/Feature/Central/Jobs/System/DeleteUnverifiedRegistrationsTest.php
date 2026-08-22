<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Jobs\System;

use App\Jobs\System\DeleteUnverifiedRegistrations;
use App\Models\Registration;
use Tests\Feature\Central\CentralTestCase;

class DeleteUnverifiedRegistrationsTest extends CentralTestCase
{
    public function test_it_deletes_unverified_registrations_older_than_three_days(): void
    {
        $stale = new Registration([
            'organization' => 'Stale Org',
            'email' => 'stale@example.com',
        ]);
        $stale->created_at = now()->subDays(4);
        $stale->save();

        (new DeleteUnverifiedRegistrations)->handle();

        $this->assertDatabaseMissing(Registration::class, [
            'email' => 'stale@example.com',
        ]);
    }

    public function test_it_keeps_recent_and_verified_registrations(): void
    {
        $recent = new Registration([
            'organization' => 'Recent Org',
            'email' => 'recent@example.com',
        ]);
        $recent->created_at = now()->subDay();
        $recent->save();

        $verified = new Registration([
            'organization' => 'Verified Org',
            'email' => 'verified@example.com',
            'verified_at' => now()->subWeek(),
        ]);
        $verified->created_at = now()->subWeek();
        $verified->save();

        (new DeleteUnverifiedRegistrations)->handle();

        $this->assertDatabaseHas(Registration::class, [
            'email' => 'recent@example.com',
        ]);
        $this->assertDatabaseHas(Registration::class, [
            'email' => 'verified@example.com',
        ]);
    }

    public function test_it_uses_the_system_queue(): void
    {
        $this->assertSame('system', (new DeleteUnverifiedRegistrations)->queue);
    }
}
