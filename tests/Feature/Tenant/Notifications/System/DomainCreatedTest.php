<?php

namespace Tests\Feature\Tenant\Notifications\System;

use App\Notifications\System\DomainCreated;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class DomainCreatedTest extends TenantTestCase
{
    public function test_domain_created_notification_is_sent()
    {
        Notification::fake();

        $this->tenant->domains()->create([
            'domain' => $this->faker->domainWord,
        ]);

        Notification::assertSentTo($this->tenant, DomainCreated::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->tenant);
            $mail->assertTo($this->tenant->email);

            return true;
        });
    }
}
