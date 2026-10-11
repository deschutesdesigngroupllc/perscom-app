<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Notifications;

use App\Models\AssignmentRecord;
use App\Models\AwardRecord;
use App\Models\CombatRecord;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Models\Image;
use App\Models\Message;
use App\Models\ModelNotification;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Notifications\Tenant\NewAssignmentRecord;
use App\Notifications\Tenant\NewAwardRecord;
use App\Notifications\Tenant\NewCombatRecord;
use App\Notifications\Tenant\NewMessage;
use App\Notifications\Tenant\NewModelNotification;
use App\Notifications\Tenant\NewQualificationRecord;
use App\Notifications\Tenant\NewRankRecord;
use App\Notifications\Tenant\NewServiceRecord;
use App\Notifications\Tenant\UpcomingEvent;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Tenant\TenantTestCase;

class DiscordEmbedNotificationTest extends TenantTestCase
{
    public function test_rank_record_notification_sends_rich_embed(): void
    {
        $record = RankRecord::factory()->for(User::factory()->createQuietly())->createQuietly();

        $message = new NewRankRecord($record)->toDiscord();

        $this->assertSame('', $message->body);
        $this->assertSame('New Rank Record', $message->embed['title']);
        $this->assertArrayHasKey('url', $message->embed);
        $this->assertArrayHasKey('color', $message->embed);
        $this->assertArrayHasKey('timestamp', $message->embed);
        $this->assertContains(['name' => 'Member', 'value' => $record->user->name, 'inline' => true], $message->embed['fields']);
        $this->assertContains(['name' => 'Rank', 'value' => $record->rank->name, 'inline' => true], $message->embed['fields']);
    }

    public function test_award_record_notification_includes_award_image_thumbnail(): void
    {
        Storage::fake();

        $record = AwardRecord::factory()->for(User::factory()->createQuietly())->createQuietly();
        $record->award->image()->save(Image::factory()->make(['path' => 'images/award.png']));

        $message = new NewAwardRecord($record->fresh())->toDiscord();

        $this->assertSame('New Award Record', $message->embed['title']);
        $this->assertArrayHasKey('thumbnail', $message->embed);
        $this->assertStringContainsString('images/award.png', $message->embed['thumbnail']['url']);
        $this->assertContains(['name' => 'Award', 'value' => $record->award->name, 'inline' => true], $message->embed['fields']);
    }

    public function test_qualification_record_notification_sends_rich_embed(): void
    {
        $record = QualificationRecord::factory()->for(User::factory()->createQuietly())->createQuietly();

        $message = new NewQualificationRecord($record)->toDiscord();

        $this->assertSame('New Qualification Record', $message->embed['title']);
        $this->assertContains(['name' => 'Qualification', 'value' => $record->qualification->name, 'inline' => true], $message->embed['fields']);
    }

    public function test_assignment_record_notification_sends_rich_embed(): void
    {
        $record = AssignmentRecord::factory()->for(User::factory()->createQuietly())->createQuietly();

        $message = new NewAssignmentRecord($record)->toDiscord();

        $this->assertSame('New Assignment Record', $message->embed['title']);
        $this->assertContains(['name' => 'Member', 'value' => $record->user->name, 'inline' => true], $message->embed['fields']);
    }

    public function test_combat_and_service_record_notifications_send_rich_embeds(): void
    {
        $user = User::factory()->createQuietly();

        $combat = new NewCombatRecord(CombatRecord::factory()->for($user)->createQuietly())->toDiscord();
        $service = new NewServiceRecord(ServiceRecord::factory()->for($user)->createQuietly())->toDiscord();

        $this->assertSame('New Combat Record', $combat->embed['title']);
        $this->assertSame('New Service Record', $service->embed['title']);
        $this->assertNotSame($combat->embed['color'], $service->embed['color']);
    }

    public function test_model_notification_sends_rich_embed_linking_to_model(): void
    {
        $user = User::factory()->createQuietly();
        $record = RankRecord::factory()->for($user)->createQuietly();

        $modelNotification = $record->modelNotifications()->create(
            ModelNotification::forUser($user, 'test.event', 'Promotion Posted', '<p>Congratulations!</p>', [NotificationChannel::DISCORD_PRIVATE])
        );

        $discord = new NewModelNotification($modelNotification)->toDiscord();

        $this->assertSame('Promotion Posted', $discord->embed['title']);
        $this->assertStringContainsString('Congratulations!', $discord->embed['description']);
        $this->assertSame($record->url, $discord->embed['url']);
    }

    public function test_message_notification_sends_rich_embed(): void
    {
        $message = Message::factory()->createQuietly(['message' => '<p>Formation at <strong>1900</strong>.</p>']);

        $discord = new NewMessage($message)->toDiscord();

        $this->assertSame('New Message', $discord->embed['title']);
        $this->assertStringContainsString('**1900**', $discord->embed['description']);
    }

    public function test_upcoming_event_notification_sends_rich_embed(): void
    {
        $user = User::factory()->createQuietly();
        $event = Event::factory()->createQuietly(['location' => 'Range 4']);

        $discord = new UpcomingEvent($event, NotificationInterval::PT1H)->toDiscord($user);

        $this->assertSame('Upcoming Event - '.$event->name, $discord->embed['title']);
        $this->assertSame($event->name.' is starting in 1 hour.', $discord->embed['description']);
        $this->assertContains(['name' => 'Location', 'value' => 'Range 4', 'inline' => true], $discord->embed['fields']);
        $this->assertContains(['name' => 'Starts', 'value' => sprintf('<t:%d:F>', $event->starts->getTimestamp()), 'inline' => true], $discord->embed['fields']);
    }
}
