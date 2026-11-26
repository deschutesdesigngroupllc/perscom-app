<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Widgets;

use App\Filament\App\Resources\AnnouncementResource\Widgets\RecentAnnouncements;
use App\Filament\App\Resources\NewsfeedResource\Widgets\RecentNews;
use App\Models\Announcement;
use App\Models\Newsfeed;
use App\Models\User;
use App\Services\RssFeedService;
use App\Support\Brand;
use BezhanSalleh\FilamentShield\Support\Utils;
use Carbon\CarbonInterface;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class RecentPostsWidgetTest extends TenantTestCase
{
    public function test_recent_news_features_the_latest_item_before_older_ones(): void
    {
        $this->actingAsSuperAdmin();
        $author = User::factory()->createQuietly(['name' => 'Captain Reyes']);
        $this->createNews($author, 'Promotions Approved', '<p>Board results are out.</p>', now()->subDay());
        $this->createNews($author, 'Detachment Reaches Full Strength', '<p>All positions are filled.</p>');

        Livewire::test(RecentNews::class)->assertSeeInOrder([
            'Latest',
            'Detachment Reaches Full Strength',
            'All positions are filled.',
            'Captain Reyes',
            'Promotions Approved',
        ]);
    }

    public function test_recent_news_excerpts_drop_html(): void
    {
        $this->actingAsSuperAdmin();
        $this->createNews(User::factory()->createQuietly(), 'Range Day', '<p>All positions are filled.</p>');

        Livewire::test(RecentNews::class)
            ->assertSee('All positions are filled.')
            ->assertDontSeeHtml('<p>All positions are filled.</p>');
    }

    public function test_recent_news_links_to_all_news_and_the_members_rss_feed(): void
    {
        $user = $this->actingAsSuperAdmin();
        $this->createNews(User::factory()->createQuietly(), 'Range Day', 'Range day is on Saturday.');

        Livewire::test(RecentNews::class)
            ->assertSee('View All News')
            ->assertSeeHtml(RssFeedService::newsFeedUrl($user));
    }

    public function test_news_without_a_color_shows_a_neutral_accent(): void
    {
        $this->actingAsSuperAdmin();
        $this->createNews(User::factory()->createQuietly(), 'Range Day', 'Range day is on Saturday.');

        Livewire::test(RecentNews::class)
            ->assertSeeHtml('bg-gray-300 dark:bg-gray-600')
            ->assertDontSeeHtml('background-color: '.Brand::PRIMARY_HEX);
    }

    public function test_opening_a_post_shows_it_in_a_modal(): void
    {
        $this->actingAsSuperAdmin();
        $news = $this->createNews(User::factory()->createQuietly(), 'Range Day', '<p>All positions are filled.</p>');

        Livewire::test(RecentNews::class)
            ->mountAction(TestAction::make('view')->arguments(['record' => $news->getKey()]))
            ->assertActionMounted('view')
            ->assertSee('All positions are filled.');
    }

    public function test_opening_a_post_deleted_after_the_dashboard_loaded_does_nothing(): void
    {
        $this->actingAsSuperAdmin();
        $news = $this->createNews(User::factory()->createQuietly(), 'Range Day', 'Range day is on Saturday.');

        $widget = Livewire::test(RecentNews::class);
        $news->delete();

        $widget
            ->mountAction(TestAction::make('view')->arguments(['record' => $news->getKey()]))
            ->assertOk()
            ->assertActionNotMounted('view');
    }

    public function test_recent_announcements_show_expiry_and_an_empty_state(): void
    {
        $this->actingAsSuperAdmin();

        Livewire::test(RecentAnnouncements::class)
            ->assertSee('No announcements yet')
            ->assertDontSee('View All Announcements');

        Announcement::factory()->createQuietly([
            'title' => 'Range Day Moved',
            'content' => 'Range day is now Saturday.',
            'expires_at' => now()->addDays(3),
        ]);

        Livewire::test(RecentAnnouncements::class)
            ->assertSee('Range Day Moved')
            ->assertSee('Expires')
            ->assertSee('View All Announcements');
    }

    public function test_the_rss_link_is_only_offered_to_members_whose_feed_would_load(): void
    {
        $member = User::factory()->createQuietly()->syncRoles([])->givePermissionTo([
            'view_recent_news',
            'view_recent_announcements',
        ]);
        $this->actingAs($member);

        Livewire::test(RecentNews::class)->assertDontSee('news.xml');
        Livewire::test(RecentAnnouncements::class)->assertDontSee('announcements.xml');

        $member->givePermissionTo(['view_newsfeed', 'view_announcement']);

        Livewire::test(RecentNews::class)->assertSee('news.xml');
        Livewire::test(RecentAnnouncements::class)->assertSee('announcements.xml');
    }

    private function createNews(
        User $author,
        string $headline,
        string $text,
        ?CarbonInterface $createdAt = null,
    ): Newsfeed {
        return Newsfeed::factory()->createQuietly([
            'causer_type' => User::class,
            'causer_id' => $author->getKey(),
            'created_at' => $createdAt ?? now(),
            'properties' => ['headline' => $headline, 'text' => $text],
        ]);
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
