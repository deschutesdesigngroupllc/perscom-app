<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\App\Feeds;

use App\Models\Announcement;
use App\Models\Newsfeed;
use App\Models\User;
use App\Services\CalendarFeedService;
use App\Services\RssFeedService;
use SimpleXMLElement;
use Tests\Feature\Tenant\TenantTestCase;

class RssFeedControllerTest extends TenantTestCase
{
    public function test_the_announcements_feed_is_valid_rss_without_a_session(): void
    {
        $user = $this->member(['view_announcement']);
        Announcement::factory()->createQuietly(['title' => 'Range Day Moved', 'content' => '<p>Now on Saturday & Sunday.</p>']);

        $response = $this->get(RssFeedService::announcementsFeedUrl($user))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8');

        $rss = new SimpleXMLElement((string) $response->getContent());

        $this->assertSame('2.0', (string) $rss['version']);
        $this->assertSame('Range Day Moved', (string) $rss->channel->item[0]->title);
        $this->assertSame('<p>Now on Saturday & Sunday.</p>', (string) $rss->channel->item[0]->description);
    }

    public function test_the_news_feed_lists_items_with_their_author(): void
    {
        $user = $this->member(['view_newsfeed']);
        Newsfeed::factory()->createQuietly([
            'causer_type' => User::class,
            'causer_id' => $user->getKey(),
            'properties' => ['headline' => 'Promotions Approved', 'text' => 'Board results are out.'],
        ]);

        $content = (string) $this->get(RssFeedService::newsFeedUrl($user))->assertOk()->getContent();
        $rss = new SimpleXMLElement($content);

        $this->assertSame('Promotions Approved', (string) $rss->channel->item[0]->title);
        $this->assertSame($user->name, (string) $rss->channel->item[0]->children('dc', true)->creator);
    }

    public function test_an_unknown_token_is_not_found(): void
    {
        $this->get(route('app.rss-feeds.news', ['token' => str_repeat('x', 40)]))->assertNotFound();
        $this->get(route('app.rss-feeds.announcements', ['token' => str_repeat('x', 40)]))->assertNotFound();
    }

    public function test_members_without_permission_are_refused(): void
    {
        $user = $this->member([]);

        $this->get(RssFeedService::newsFeedUrl($user))->assertForbidden();
        $this->get(RssFeedService::announcementsFeedUrl($user))->assertForbidden();
    }

    public function test_resetting_the_feed_token_invalidates_old_rss_links(): void
    {
        $user = $this->member(['view_newsfeed']);
        $url = RssFeedService::newsFeedUrl($user);

        CalendarFeedService::resetToken($user);

        $this->get($url)->assertNotFound();
        $this->get(RssFeedService::newsFeedUrl($user))->assertOk();
    }

    /**
     * @param  array<int, string>  $permissions
     */
    private function member(array $permissions): User
    {
        $user = User::factory()->createQuietly()->syncRoles([]);

        if ($permissions !== []) {
            $user->givePermissionTo($permissions);
        }

        return $user;
    }
}
