<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Clusters\Settings\Pages;

use App\Actions\Organization\ApplyOrganizationBlueprint;
use App\Ai\Agents\OrganizationBlueprintAgent;
use App\Data\Organization\OrganizationBlueprintData;
use App\Filament\App\Clusters\Settings\Pages\OrganizationBuilder;
use App\Models\AiUsage;
use App\Models\Group;
use App\Models\Rank;
use App\Models\Unit;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Laravel\Ai\Prompts\AgentPrompt;
use Livewire\Livewire;
use Tests\Feature\Tenant\Services\Ai\OrganizationBlueprintServiceTest;
use Tests\Feature\Tenant\TenantTestCase;

class OrganizationBuilderTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('app');

        config([
            'ai.default' => 'anthropic',
            'ai.providers.anthropic.key' => 'test-key',
            'ai.limits.monthly_tokens' => 100000,
        ]);
    }

    public function test_only_super_admins_can_access_the_page(): void
    {
        $this->actingAs(User::factory()->createQuietly());

        $this->assertFalse(OrganizationBuilder::canAccess());

        $this->actingAsSuperAdmin();

        $this->assertTrue(OrganizationBuilder::canAccess());
    }

    public function test_super_admin_can_propose_and_apply_additions(): void
    {
        $existing = Group::factory()->createQuietly(['name' => 'Airborne']);

        OrganizationBlueprintAgent::fake([OrganizationBlueprintServiceTest::structuredResponse()]);

        $this->actingAsSuperAdmin();

        Livewire::test(OrganizationBuilder::class)
            ->assertSuccessful()
            ->set('data.instruction', 'Add a rifle platoon and an airborne rank structure.')
            ->call('propose')
            ->assertNotified('Proposal Ready')
            ->assertSet('data.blueprint.ranks.0.name', 'Captain')
            ->call('apply')
            ->assertNotified('Proposal Applied')
            ->assertSet('data.blueprint.ranks', []);

        OrganizationBlueprintAgent::assertPrompted(fn (AgentPrompt $prompt): bool => $prompt->contains('Add a rifle platoon')
            && $prompt->contains('Airborne'));

        $this->assertDatabaseHas(Rank::class, ['name' => 'Captain']);
        $this->assertTrue(Unit::query()->where('name', '1st Platoon')->firstOrFail()->groups()->whereKey($existing->getKey())->exists());
        $this->assertSame(1, Group::query()->where('name', 'Airborne')->count());
    }

    public function test_proposals_require_an_instruction(): void
    {
        OrganizationBlueprintAgent::fake();

        $this->actingAsSuperAdmin();

        Livewire::test(OrganizationBuilder::class)
            ->call('propose')
            ->assertNotified('Description Required');

        OrganizationBlueprintAgent::assertNeverPrompted();
    }

    public function test_reaching_the_ai_limit_shows_a_friendly_notification(): void
    {
        OrganizationBlueprintAgent::fake();

        AiUsage::factory()->createQuietly(['input_tokens' => 100000, 'output_tokens' => 0]);

        $this->actingAsSuperAdmin();

        Livewire::test(OrganizationBuilder::class)
            ->set('data.instruction', 'Add a medical platoon.')
            ->call('propose')
            ->assertNotified('AI Limit Reached');

        OrganizationBlueprintAgent::assertNeverPrompted();
    }

    public function test_describe_section_is_hidden_when_ai_is_unavailable(): void
    {
        config(['ai.providers.anthropic.key' => null]);

        $this->actingAsSuperAdmin();

        Livewire::test(OrganizationBuilder::class)
            ->assertDontSee('Propose Additions')
            ->assertSee('AI generation is not available');
    }

    public function test_super_admin_can_import_and_apply_a_preset(): void
    {
        $preset = OrganizationBlueprintData::fromPreset('fire-service');

        $this->actingAsSuperAdmin();

        Livewire::test(OrganizationBuilder::class)
            ->set('data.preset', 'fire-service')
            ->call('importPreset')
            ->assertNotified('Preset Imported')
            ->call('apply')
            ->assertNotified('Proposal Applied');

        $this->assertSame(count($preset->ranks), Rank::query()->whereIn('name', collect($preset->ranks)->pluck('name'))->count());
        $this->assertDatabaseHas(Group::class, ['name' => $preset->groups[0]->name]);
    }

    public function test_super_admin_can_export_the_current_organization(): void
    {
        ApplyOrganizationBlueprint::handle(OrganizationBlueprintData::fromPreset('military'));

        $this->actingAsSuperAdmin();

        Livewire::test(OrganizationBuilder::class)
            ->callAction('exportBlueprint')
            ->assertFileDownloaded('organization-blueprint.json');
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
