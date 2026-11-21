<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\FormResource\Pages\EditForm;
use App\Models\Enums\NotificationChannel;
use App\Models\Form;
use App\Models\ModelNotification;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class FormResourceModelNotificationTest extends TenantTestCase
{
    public function test_saving_notification_settings_stores_the_written_message(): void
    {
        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);

        $form = Form::factory()->createQuietly();
        $recipient = User::factory()->createQuietly();

        Livewire::test(EditForm::class, ['record' => $form->getKey()])
            ->set('data.model_notifications.enabled', true)
            ->set('data.model_notifications.users', [$recipient->getKey()])
            ->set('data.model_notifications.subject', 'New application received')
            ->set('data.model_notifications.message', '<p>Review it in the submissions queue.</p>')
            ->set('data.model_notifications.channels', [NotificationChannel::DATABASE->value])
            ->call('save')
            ->assertHasNoFormErrors();

        $notification = ModelNotification::query()->where('user_id', $recipient->getKey())->sole();

        $this->assertSame('New application received', $notification->subject);
        $this->assertSame('<p>Review it in the submissions queue.</p>', $notification->message);
    }
}
