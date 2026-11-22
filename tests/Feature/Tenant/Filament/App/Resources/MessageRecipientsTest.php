<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\MessageResource\Pages\CreateMessage;
use App\Models\Enums\NotificationChannel;
use App\Models\Message;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class MessageRecipientsTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_everyone_is_an_explicit_choice_that_stores_no_recipient_list(): void
    {
        Notification::fake();
        Queue::fake();

        Livewire::test(CreateMessage::class)
            ->assertSee('Everyone (')
            ->fillForm([
                'message' => '<p>Formation at 0600.</p>',
                'channels' => [NotificationChannel::DATABASE->value],
                'audience' => 'everyone',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertNull(Message::query()->latest('id')->firstOrFail()->recipients);
    }

    public function test_specific_members_requires_choosing_them(): void
    {
        Livewire::test(CreateMessage::class)
            ->fillForm([
                'message' => '<p>Formation at 0600.</p>',
                'channels' => [NotificationChannel::DATABASE->value],
                'audience' => 'specific',
                'recipients' => [],
            ])
            ->call('create')
            ->assertHasFormErrors(['recipients' => 'required']);
    }

    public function test_specific_members_are_stored(): void
    {
        Notification::fake();
        Queue::fake();

        $member = User::factory()->createQuietly();

        Livewire::test(CreateMessage::class)
            ->fillForm([
                'message' => '<p>Formation at 0600.</p>',
                'channels' => [NotificationChannel::DATABASE->value],
                'audience' => 'specific',
                'recipients' => [$member->getKey()],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertEquals([$member->getKey()], Message::query()->latest('id')->firstOrFail()->recipients?->map(fn ($id): int => (int) $id)->all());
    }
}
