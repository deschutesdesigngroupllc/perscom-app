<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\App\ServiceProfiles;

use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Rank;
use App\Models\User;
use App\Services\ServiceProfileService;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use Illuminate\Support\Str;
use Tests\Feature\Tenant\TenantTestCase;

class ShowControllerTest extends TenantTestCase
{
    public function test_profile_is_not_found_when_public_profiles_are_disabled(): void
    {
        $this->enablePublicProfiles(false);

        $this->get(ServiceProfileService::url($this->member()))->assertNotFound();
    }

    public function test_public_profile_shows_rank_awards_and_share_metadata(): void
    {
        $this->enablePublicProfiles();

        $member = $this->member([
            'name' => 'Jabari Willms',
            'rank_id' => Rank::factory()->createQuietly(['name' => 'Command Sergeant Major', 'abbreviation' => 'CSM'])->getKey(),
        ]);

        AwardRecord::factory()->createQuietly([
            'user_id' => $member->getKey(),
            'award_id' => Award::factory()->createQuietly(['name' => 'Bronze Star Medal'])->getKey(),
            'text' => 'For meritorious service during Exercise Robin Sage.',
        ]);

        $this->get(ServiceProfileService::url($member))
            ->assertOk()
            ->assertSee('Jabari Willms')
            ->assertSee('Command Sergeant Major')
            ->assertSee('Bronze Star Medal')
            ->assertSee('For meritorious service during Exercise Robin Sage.')
            ->assertSee('<meta property="og:title" content="CSM Jabari Willms" />', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image" />', false)
            ->assertSee('Start your unit')
            ->assertDontSee($member->email);
    }

    public function test_hidden_profile_fields_are_not_shown(): void
    {
        $this->enablePublicProfiles(hiddenFields: ['award_records']);

        $member = $this->member();

        AwardRecord::factory()->createQuietly([
            'user_id' => $member->getKey(),
            'award_id' => Award::factory()->createQuietly(['name' => 'Secret Commendation'])->getKey(),
        ]);

        $this->get(ServiceProfileService::url($member))
            ->assertOk()
            ->assertDontSee('Secret Commendation')
            ->assertDontSee('Ribbon Rack');
    }

    public function test_members_who_hide_their_profile_are_not_found(): void
    {
        $this->enablePublicProfiles();

        $this->get(ServiceProfileService::url($this->member(['hide_public_profile' => true])))->assertNotFound();
    }

    public function test_unapproved_members_are_not_found(): void
    {
        $this->enablePublicProfiles();

        $this->get(ServiceProfileService::url($this->member(['approved' => false])))->assertNotFound();
    }

    public function test_profiles_cannot_be_found_by_member_id(): void
    {
        $this->enablePublicProfiles();

        $member = $this->member();
        ServiceProfileService::url($member);

        $this->get(route('app.service-profiles.show', ['handle' => (string) $member->getKey()]))->assertNotFound();
        $this->get(route('app.service-profiles.show', ['handle' => Str::slug($member->name)]))->assertNotFound();
    }

    public function test_profile_handles_use_a_random_key_that_survives_name_changes(): void
    {
        $this->enablePublicProfiles();

        $member = $this->member(['name' => 'Jane Doe']);
        $handle = ServiceProfileService::handle($member);

        $this->assertMatchesRegularExpression('/^jane-doe-[a-z0-9]{10}$/', $handle);

        $member->update(['name' => 'Jane Smith']);

        $this->assertSame('jane-smith-'.$member->public_profile_key, ServiceProfileService::handle($member));

        $this->get(route('app.service-profiles.show', ['handle' => $handle]))
            ->assertRedirect(ServiceProfileService::url($member));
    }

    public function test_profile_key_is_not_exposed_when_serializing_members(): void
    {
        $member = $this->member();
        ServiceProfileService::key($member);

        $this->assertArrayNotHasKey('public_profile_key', $member->toArray());
    }

    public function test_share_image_is_a_png(): void
    {
        $this->enablePublicProfiles();

        $response = $this->get(ServiceProfileService::imageUrl($this->member()))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $size = getimagesizefromstring((string) $response->getContent());

        $this->assertSame([1200, 630], [$size[0] ?? null, $size[1] ?? null]);
    }

    /**
     * @param  string[]  $hiddenFields
     */
    protected function enablePublicProfiles(bool $enabled = true, array $hiddenFields = []): void
    {
        DashboardSettings::fake([
            'public_profiles_enabled' => $enabled,
            'user_hidden_fields' => $hiddenFields,
        ]);

        SettingsService::flush(DashboardSettings::class);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function member(array $attributes = []): User
    {
        return User::factory()->createQuietly(['approved' => true, ...$attributes]);
    }
}
