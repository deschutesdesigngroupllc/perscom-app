<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Livewire\App;

use App\Livewire\App\AnnouncementBanner;
use App\Models\Announcement;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class AnnouncementBannerTest extends TenantTestCase
{
    public function test_global_announcements_are_shown_but_hidden_when_printing(): void
    {
        Announcement::factory()->createQuietly(['title' => 'Exercise Robin Sage', 'global' => true]);

        Livewire::test(AnnouncementBanner::class)
            ->assertSee('Exercise Robin Sage')
            ->assertSeeHtml('class="print:hidden"');
    }
}
