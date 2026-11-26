<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\App\Feeds;

use App\Models\Announcement;
use App\Models\User;
use App\Services\RssFeedService;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Tests\Feature\Tenant\TenantTestCase;

class AnnouncementsFeedControllerTest extends TenantTestCase
{
    public function test_the_feed_is_valid_rss_without_a_session(): void
    {
        $user = User::factory()->createQuietly()->syncRoles([])->givePermissionTo('view_announcement');
        Announcement::factory()->createQuietly([
            'title' => 'Range Day Moved',
            'content' => '<p>Now on Saturday & Sunday.</p>',
        ]);

        $response = $this->get(RssFeedService::announcementsFeedUrl($user))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8');

        $rss = new SimpleXMLElement((string) $response->getContent());

        $this->assertSame('2.0', (string) $rss['version']);
        $this->assertSame('Range Day Moved', (string) $rss->channel->item[0]->title);
        $this->assertSame('<p>Now on Saturday & Sunday.</p>', (string) $rss->channel->item[0]->description);
    }

    public function test_an_unknown_token_is_not_found(): void
    {
        $this->get(route('app.rss-feeds.announcements', ['token' => Str::repeat('x', 40)]))->assertNotFound();
    }

    public function test_members_without_permission_are_refused(): void
    {
        $user = User::factory()->createQuietly()->syncRoles([]);

        $this->get(RssFeedService::announcementsFeedUrl($user))->assertForbidden();
    }
}
