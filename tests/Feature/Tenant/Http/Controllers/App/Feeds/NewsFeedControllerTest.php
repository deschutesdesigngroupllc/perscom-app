<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\App\Feeds;

use App\Models\Newsfeed;
use App\Models\User;
use App\Services\CalendarFeedService;
use App\Services\RssFeedService;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Tests\Feature\Tenant\TenantTestCase;

class NewsFeedControllerTest extends TenantTestCase
{
    public function test_the_feed_lists_items_with_their_author(): void
    {
        $user = User::factory()->createQuietly()->syncRoles([])->givePermissionTo('view_newsfeed');
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
        $this->get(route('app.rss-feeds.news', ['token' => Str::repeat('x', 40)]))->assertNotFound();
    }

    public function test_members_without_permission_are_refused(): void
    {
        $user = User::factory()->createQuietly()->syncRoles([]);

        $this->get(RssFeedService::newsFeedUrl($user))->assertForbidden();
    }

    public function test_resetting_the_feed_token_invalidates_old_links(): void
    {
        $user = User::factory()->createQuietly()->syncRoles([])->givePermissionTo('view_newsfeed');
        $url = RssFeedService::newsFeedUrl($user);

        CalendarFeedService::resetToken($user);

        $this->get($url)->assertNotFound();
        $this->get(RssFeedService::newsFeedUrl($user))->assertOk();
    }
}
