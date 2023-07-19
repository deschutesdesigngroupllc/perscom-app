<?php

namespace Tests\Feature\Tenant\Notifications\System;

use App\Notifications\System\DomainDeleted;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class DomainDeletedTest extends TenantTestCase
{
    public function test_domain_deleted_notification_is_sent()
    {
        Notification::fake();

        $domain = $this->tenant->domains()->create([
            'is_custom_subdomain' => true,
            'domain' => $this->faker->domainWord,
        ]);
        $domain->delete();

        Notification::assertSentTo($this->tenant, DomainDeleted::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->tenant);
            $mail->assertTo($this->tenant->email);

            return true;
        });
    }
}
