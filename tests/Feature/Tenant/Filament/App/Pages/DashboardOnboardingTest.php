<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages;

use App\Ai\Agents\OrganizationBlueprintAgent;
use App\Data\Organization\OrganizationBlueprintData;
use App\Filament\App\Pages\Dashboard;
use App\Models\Award;
use App\Models\Group;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\Rank;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use App\Settings\OnboardingSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Testing\TestAction;
use Laravel\Ai\Prompts\AgentPrompt;
use Livewire\Livewire;
use Tests\Feature\Tenant\Services\Ai\OrganizationBlueprintServiceTest;
use Tests\Feature\Tenant\TenantTestCase;

class DashboardOnboardingTest extends TenantTestCase
{
    public function test_onboarding_action_is_visible_for_super_admin_when_accessible(): void
    {
        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        Livewire::test(Dashboard::class)
            ->assertActionVisible('onboarding');
    }

    public function test_onboarding_action_is_hidden_when_not_accessible(): void
    {
        OnboardingSettings::fake([
            'completed' => false,
            'dismissed' => true,
            'completed_at' => null,
        ]);

        $this->actingAsSuperAdmin();

        Livewire::test(Dashboard::class)
            ->assertActionHidden('onboarding');
    }

    public function test_dont_show_again_permanently_dismisses_onboarding(): void
    {
        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        (new Dashboard)->dismissOnboarding();

        $settings = resolve(OnboardingSettings::class);

        $this->assertTrue($settings->dismissed);
        $this->assertFalse($settings->isAccessible());
    }

    public function test_completing_onboarding_creates_the_organization_structure(): void
    {
        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        Livewire::test(Dashboard::class)
            ->callAction('onboarding', data: [
                'group' => ['name' => 'Operations', 'description' => 'Operational units.'],
                'unit' => ['name' => 'Alpha Company', 'description' => 'First company.'],
                'position' => ['name' => 'Platoon Leader'],
                'rank' => ['name' => 'Sergeant', 'abbreviation' => 'SGT', 'paygrade' => 'E-5'],
                'status' => ['name' => 'Active Duty', 'color' => '#16a34a'],
                'finish' => ['dont_show_again' => true],
            ])
            ->assertHasNoActionErrors();

        $unit = Unit::query()->where('name', 'Alpha Company')->firstOrFail();

        $this->assertSame('Operations', $unit->groups()->value('name'));
        $this->assertDatabaseHas(Group::class, ['name' => 'Operations', 'description' => 'Operational units.']);
        $this->assertDatabaseHas(Position::class, ['name' => 'Platoon Leader']);
        $this->assertDatabaseHas(Rank::class, ['name' => 'Sergeant', 'abbreviation' => 'SGT', 'paygrade' => 'E-5']);
        $this->assertDatabaseHas(Status::class, ['name' => 'Active Duty', 'color' => '#16a34a']);
        $this->assertTrue(resolve(OnboardingSettings::class)->completed);
    }

    public function test_completing_onboarding_reuses_existing_records(): void
    {
        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        $group = Group::factory()->createQuietly(['name' => 'Operations']);
        $unit = Unit::factory()->createQuietly(['name' => 'Alpha Company']);
        $rank = Rank::factory()->createQuietly(['name' => 'Sergeant']);

        Livewire::test(Dashboard::class)
            ->callAction('onboarding', data: [
                'group' => ['name' => 'operations'],
                'unit' => ['name' => 'alpha company'],
                'rank' => ['name' => 'SERGEANT'],
                'finish' => ['dont_show_again' => false],
            ])
            ->assertHasNoActionErrors();

        $this->assertSame(1, Group::query()->where('name', 'Operations')->count());
        $this->assertSame(1, Unit::query()->where('name', 'Alpha Company')->count());
        $this->assertSame(1, Rank::query()->where('name', 'Sergeant')->count());
        $this->assertTrue($unit->groups()->whereKey($group->getKey())->exists());
        $this->assertSame('Sergeant', $rank->refresh()->name);
        $this->assertTrue(resolve(OnboardingSettings::class)->dismissed);
    }

    public function test_onboarding_can_apply_a_preset_without_ai(): void
    {
        config(['ai.providers.anthropic.key' => null]);

        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        $preset = OrganizationBlueprintData::fromPreset('law-enforcement');

        Livewire::test(Dashboard::class)
            ->mountAction('onboarding')
            ->assertMountedActionModalDontSee('Generate Structure')
            ->assertMountedActionModalSee('AI generation is not available')
            ->fillForm(['builder' => ['preset' => 'law-enforcement']])
            ->callAction(TestAction::make('loadPreset')->schemaComponent('onboardingBuilderActions'))
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $this->assertSame(count($preset->ranks), Rank::query()->whereIn('name', collect($preset->ranks)->pluck('name'))->count());
        $this->assertDatabaseHas(Group::class, ['name' => $preset->groups[0]->name]);
        $this->assertDatabaseHas(Unit::class, ['name' => $preset->groups[0]->units[0]->name]);
        $this->assertTrue(resolve(OnboardingSettings::class)->completed);
    }

    public function test_manual_structure_steps_are_hidden_once_a_preset_is_loaded(): void
    {
        config(['ai.providers.anthropic.key' => null]);

        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        Livewire::test(Dashboard::class)
            ->mountAction('onboarding')
            ->assertMountedActionModalSee('Create a Group')
            ->fillForm(['builder' => ['preset' => 'military']])
            ->callAction(TestAction::make('loadPreset')->schemaComponent('onboardingBuilderActions'))
            ->assertMountedActionModalDontSee('Create a Group')
            ->assertMountedActionModalDontSee('Create a Unit')
            ->assertMountedActionModalDontSee('Create a Position')
            ->assertMountedActionModalDontSee('Create a Rank')
            ->assertMountedActionModalDontSee('Create a Status')
            ->assertMountedActionModalSee('Create Personnel');
    }

    public function test_personnel_can_be_assigned_from_a_loaded_preset(): void
    {
        config(['ai.providers.anthropic.key' => null]);

        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        $preset = OrganizationBlueprintData::fromPreset('law-enforcement');
        $unitName = $preset->groups[0]->units[0]->name;
        $rankName = $preset->ranks[0]->name;

        Livewire::test(Dashboard::class)
            ->mountAction('onboarding')
            ->fillForm(['builder' => ['preset' => 'law-enforcement']])
            ->callAction(TestAction::make('loadPreset')->schemaComponent('onboardingBuilderActions'))
            ->fillForm([
                'personnel' => [
                    'name' => 'Jane Doe',
                    'email' => 'jane.doe@example.com',
                    'assign_unit' => $unitName,
                    'assign_rank' => $rankName,
                ],
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $user = User::query()->where('email', 'jane.doe@example.com')->firstOrFail();

        $this->assertSame($unitName, $user->unit?->name);
        $this->assertSame($rankName, $user->rank?->name);
    }

    public function test_onboarding_generates_and_applies_an_ai_blueprint(): void
    {
        config([
            'ai.default' => 'anthropic',
            'ai.providers.anthropic.key' => 'test-key',
        ]);

        OrganizationBlueprintAgent::fake([OrganizationBlueprintServiceTest::structuredResponse()]);

        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        Livewire::test(Dashboard::class)
            ->mountAction('onboarding')
            ->fillForm(['builder' => ['description' => 'A WWII airborne rifle company.']])
            ->callAction(TestAction::make('generateBlueprint')->schemaComponent('onboardingBuilderActions'))
            ->assertNotified('Structure Generated')
            ->callMountedAction()
            ->assertHasNoActionErrors();

        OrganizationBlueprintAgent::assertPrompted(fn (AgentPrompt $prompt): bool => $prompt->contains('A WWII airborne rifle company.'));

        $this->assertSame(['Captain', 'Sergeant', 'Private'], Rank::query()->whereIn('name', ['Captain', 'Sergeant', 'Private'])->pluck('name')->all());
        $this->assertTrue(Unit::query()->where('name', '1st Platoon')->firstOrFail()->groups()->where('name', 'Airborne')->exists());
        $this->assertDatabaseHas(Award::class, ['name' => 'Bronze Star']);
        $this->assertDatabaseHas(Qualification::class, ['name' => 'Parachutist']);
        $this->assertDatabaseHas(Status::class, ['name' => 'Active', 'color' => '#22c55e']);
    }

    public function test_onboarding_generation_requires_a_description(): void
    {
        config([
            'ai.default' => 'anthropic',
            'ai.providers.anthropic.key' => 'test-key',
        ]);

        OrganizationBlueprintAgent::fake();

        $this->fakeAccessibleOnboarding();

        $this->actingAsSuperAdmin();

        Livewire::test(Dashboard::class)
            ->mountAction('onboarding')
            ->callAction(TestAction::make('generateBlueprint')->schemaComponent('onboardingBuilderActions'))
            ->assertNotified('Description Required');

        OrganizationBlueprintAgent::assertNeverPrompted();
    }

    private function fakeAccessibleOnboarding(): void
    {
        OnboardingSettings::fake([
            'completed' => false,
            'dismissed' => false,
            'completed_at' => null,
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
