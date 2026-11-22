<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Tenant;

use App\Actions\Invites\AcceptInvite;
use App\Actions\Tenant\SetupTenantAccount;
use App\Mail\Tenant\NewTenantMail;
use App\Models\Announcement;
use App\Models\Enums\InviteType;
use App\Models\Enums\OrganizationType;
use App\Models\Invite;
use App\Models\Page;
use App\Models\Rank;
use App\Models\User;
use App\Settings\OnboardingSettings;
use Illuminate\Support\Facades\Mail;
use ReflectionMethod;
use ReflectionProperty;
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

    public function test_founder_receives_a_set_password_link_instead_of_a_password(): void
    {
        Mail::fake();

        $this->tenant->setAttribute('founder_name', 'Jordan Reyes');
        $this->tenant->saveQuietly();

        (new SetupTenantAccount)
            ->shouldSeedSettings(false)
            ->shouldSetInitialSettings(false)
            ->shouldApplyBlueprint(false)
            ->shouldCreateNewsItem(false)
            ->shouldCreateAnnouncement(false)
            ->shouldCreatePages(false)
            ->handle($this->tenant);

        try {
            $this->tenant->run(function (): void {
                $founder = User::query()->where('email', $this->tenant->email)->firstOrFail();

                $this->assertSame('Jordan Reyes', $founder->name);
                $this->assertNotNull($founder->email_verified_at);
                $this->assertTrue($founder->approved);
                $invite = Invite::query()->pending()->where('user_id', $founder->getKey())->where('type', InviteType::CLAIM)->firstOrFail();

                $this->assertNull($founder->password);
                $this->assertTrue($invite->isRedeemable(), 'The founder\'s set-password link must be redeemable.');

                $claimed = AcceptInvite::handle($invite, ['password' => 'Correct-Horse-Battery-9', 'password_confirmation' => 'Correct-Horse-Battery-9']);

                $this->assertTrue($claimed->is($founder));
                $this->assertNotNull($claimed->refresh()->password);
            });

            Mail::assertQueued(NewTenantMail::class, function (NewTenantMail $mail): bool {
                $html = $mail->render();

                return str_contains($html, '/invitation/')
                    && str_contains($html, 'Set Your Password')
                    && ! str_contains($html, 'Password</strong>:');
            });
        } finally {
            // SetupTenantAccount writes inside $tenant->run(), which commits to the shared tenant
            // database, so the founder and their invite are removed explicitly.
            $this->tenant->setAttribute('founder_name', null);
            $this->tenant->saveQuietly();

            $this->tenant->run(function (): void {
                Invite::query()->where('email', $this->tenant->email)->delete();
                User::query()->where('email', $this->tenant->email)->get()->each->deleteQuietly();
            });
        }
    }

    public function test_applying_the_signup_blueprint_skips_the_setup_wizard(): void
    {
        OnboardingSettings::fake(['completed' => false, 'dismissed' => false, 'completed_at' => null]);

        $this->tenant->setAttribute('organization_type', OrganizationType::MILITARY->value);

        $action = new SetupTenantAccount;
        (new ReflectionProperty($action, 'tenant'))->setValue($action, $this->tenant);
        (new ReflectionMethod($action, 'applyOrganizationBlueprint'))->invoke($action);

        $this->assertTrue(Rank::query()->exists());
        $this->assertFalse(resolve(OnboardingSettings::class)->isAccessible());
    }

    public function test_no_blueprint_leaves_the_setup_wizard_available(): void
    {
        OnboardingSettings::fake(['completed' => false, 'dismissed' => false, 'completed_at' => null]);

        $this->tenant->setAttribute('organization_type', OrganizationType::OTHER->value);

        $action = new SetupTenantAccount;
        (new ReflectionProperty($action, 'tenant'))->setValue($action, $this->tenant);
        (new ReflectionMethod($action, 'applyOrganizationBlueprint'))->invoke($action);

        $this->assertTrue(resolve(OnboardingSettings::class)->isAccessible());
    }

    public function test_fluent_toggles_return_the_same_instance(): void
    {
        $action = new SetupTenantAccount;

        $this->assertSame($action, $action->shouldCreatePages(false));
    }
}
