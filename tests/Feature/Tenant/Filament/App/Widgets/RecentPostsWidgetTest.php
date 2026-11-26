<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Widgets;

use App\Filament\App\Resources\AnnouncementResource\Widgets\RecentAnnouncements;
use App\Filament\App\Resources\NewsfeedResource\Widgets\RecentNews;
use App\Models\Announcement;
use App\Models\Newsfeed;
use App\Models\User;
use App\Services\RssFeedService;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class RecentPostsWidgetTest extends TenantTestCase
{
    private User $user;

    public function test_recent_news_features_the_latest_item_and_opens_it_in_a_modal(): void
    {
        $this->user = $this->actingAsSuperAdmin();

        $author = User::factory()->createQuietly(['name' => 'Captain Reyes']);

        Newsfeed::factory()->createQuietly([
            'causer_type' => User::class,
            'causer_id' => $author->getKey(),
            'created_at' => now()->subDay(),
            'properties' => ['headline' => 'Promotions Approved', 'text' => '<p>Board results are out.</p>'],
        ]);

        $latest = Newsfeed::factory()->createQuietly([
            'causer_type' => User::class,
            'causer_id' => $author->getKey(),
            'properties' => ['headline' => 'Detachment Reaches Full Strength', 'text' => '<p>All positions are filled.</p>'],
        ]);

        $widget = Livewire::test(RecentNews::class);

        $widget->assertSeeInOrder(['Latest', 'Detachment Reaches Full Strength', 'All positions are filled.', 'Captain Reyes', 'Promotions Approved'])->assertDontSeeHtml('<p>All positions are filled.</p>')->assertSee('View All News')->assertSeeHtml(RssFeedService::newsFeedUrl($this->user));

        $widget
            ->mountAction(TestAction::make('view')->arguments(['record' => $latest->getKey()]))
            ->assertActionMounted('view')
            ->assertSee('All positions are filled.');
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

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
