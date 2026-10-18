<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Notifications\Channels;

use App\Actions\Messages\SendMessage;
use App\Models\Enums\NotificationChannel;
use App\Models\Message;
use App\Models\User;
use App\Settings\IntegrationSettings;
use App\Support\Discord\DiscordEmbed;
use App\Support\Slack\SlackBlocks;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\Feature\Tenant\TenantTestCase;

class SlackPublicChannelTest extends TenantTestCase
{
    public function test_messages_sent_to_slack_post_once_to_the_connected_channel(): void
    {
        $this->connectSlack();

        Http::fake(['slack.com/api/chat.postMessage' => Http::response(['ok' => true])]);

        User::factory()->createQuietly();

        $message = Message::factory()->createQuietly([
            'message' => '<p>Final back-briefs are <strong>tonight</strong>.</p>',
            'channels' => [NotificationChannel::SLACK],
        ]);

        SendMessage::handle($message);

        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request): bool => $request->hasHeader('Authorization', 'Bearer xoxb-test')
            && $request['channel'] === 'C123'
            && data_get($request->data(), 'blocks.0.text.text') === 'New Message'
            && str_contains((string) data_get($request->data(), 'blocks.1.text.text'), '*tonight*'));
    }

    public function test_slack_is_only_offered_once_a_channel_is_chosen(): void
    {
        $this->assertFalse(NotificationChannel::SLACK->getEnabled());

        $this->connectSlack();

        $this->assertTrue(NotificationChannel::SLACK->getEnabled());
        $this->assertTrue(NotificationChannel::SLACK->isOrganizationWide());
    }

    public function test_nothing_is_posted_when_slack_is_not_connected(): void
    {
        Http::fake();

        User::factory()->createQuietly();

        SendMessage::handle(Message::factory()->createQuietly(['channels' => [NotificationChannel::SLACK]]));

        Http::assertNothingSent();
    }

    public function test_discord_embeds_become_block_kit(): void
    {
        $embed = DiscordEmbed::make()
            ->title('New Rank Record')
            ->author('Jane Doe')
            ->description('Promoted to **Sergeant**. See [the record](https://example.test/r/1).')
            ->field('Rank', 'Sergeant')
            ->field('Unit', 'Alpha <Company>')
            ->url('https://example.test/r/1')
            ->footer('5th Special Forces Group')
            ->toArray();

        $message = SlackBlocks::fromDiscordEmbed($embed);
        $blocks = collect($message['blocks']);

        $this->assertSame('New Rank Record', $message['text']);
        $this->assertSame(['context', 'header', 'section', 'actions', 'context'], $blocks->pluck('type')->all());
        $this->assertSame('Promoted to *Sergeant*. See <https://example.test/r/1|the record>.', data_get($blocks[2], 'text.text'));
        $this->assertSame("*Unit*\nAlpha &lt;Company&gt;", data_get($blocks[2], 'fields.1.text'));
        $this->assertSame('https://example.test/r/1', data_get($blocks[3], 'elements.0.url'));
    }

    protected function connectSlack(): void
    {
        $settings = resolve(IntegrationSettings::class);
        $settings->slack_settings = [
            'slack_enabled' => true,
            'team_id' => 'T1',
            'team_name' => '5th SFG',
            'bot_token' => Crypt::encryptString('xoxb-test'),
            'slack_channel' => 'C123',
        ];
        $settings->save();
    }
}
