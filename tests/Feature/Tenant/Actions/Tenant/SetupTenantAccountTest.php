<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Tenant;

use App\Actions\Tenant\SetupTenantAccount;
use App\Models\Announcement;
use App\Models\Page;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Tenant\TenantTestCase;

class SetupTenantAccountTest extends TenantTestCase
{
    public function test_marks_the_tenant_setup_as_completed(): void
    {
        Mail::fake();

        (new SetupTenantAccount)
            ->shouldSeedSettings(false)
            ->shouldSetInitialSettings(false)
            ->shouldCreateUser(false)
            ->shouldCreateNewsItem(false)
            ->shouldCreateAnnouncement(false)
            ->shouldCreatePages(false)
            ->handle($this->tenant);

        $this->assertNotNull($this->tenant->refresh()->setup_completed_at);
    }

    public function test_creates_the_welcome_announcement_and_widget_pages(): void
    {
        Mail::fake();

        $slugs = ['awards', 'positions', 'qualifications', 'ranks', 'specialties'];

        (new SetupTenantAccount)
            ->shouldSeedSettings(false)
            ->shouldSetInitialSettings(false)
            ->shouldCreateUser(false)
            ->shouldCreateNewsItem(false)
            ->handle($this->tenant);

        try {
            $this->assertTrue(Announcement::query()->where('global', true)->exists());

            foreach ($slugs as $slug) {
                $this->assertTrue(
                    Page::query()->where('slug', $slug)->exists(),
                    "Expected the [{$slug}] widget page to be created.",
                );
            }
        } finally {
            // SetupTenantAccount writes inside $tenant->run(), which purges the
            // test transaction and commits to the shared tenant database, so the
            // seeded rows are cleaned up explicitly to keep the suite isolated.
            Page::query()->whereIn('slug', $slugs)->delete();
            Announcement::query()->where('global', true)->delete();
        }
    }

    public function test_fluent_toggles_return_the_same_instance(): void
    {
        $action = new SetupTenantAccount;

        $this->assertSame($action, $action->shouldCreatePages(false));
    }
}
