<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services\Ai;

use App\Ai\Agents\OrganizationBlueprintAgent;
use App\Data\Organization\OrganizationBlueprintData;
use App\Data\Organization\RankData;
use App\Exceptions\AiUsageLimitExceededException;
use App\Models\AiUsage;
use App\Models\Group;
use App\Models\Rank;
use App\Models\Unit;
use App\Services\Ai\OrganizationBlueprintService;
use Laravel\Ai\Prompts\AgentPrompt;
use Tests\Feature\Tenant\TenantTestCase;

class OrganizationBlueprintServiceTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ai.default' => 'anthropic',
            'ai.providers.anthropic.key' => 'test-key',
            'ai.limits.monthly_tokens' => 100000,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function structuredResponse(): array
    {
        return [
            'name' => 'Easy Company',
            'description' => 'A WWII airborne rifle company.',
            'positions' => [['name' => 'Rifleman', 'description' => null]],
            'specialties' => [['name' => 'Infantryman', 'abbreviation' => '745', 'description' => null]],
            'ranks' => [
                ['name' => 'Captain', 'abbreviation' => 'CPT', 'paygrade' => 'O-3', 'description' => null, 'category' => 'Officers'],
                ['name' => 'Sergeant', 'abbreviation' => 'SGT', 'paygrade' => 'E-5', 'description' => null, 'category' => 'Enlisted'],
                ['name' => 'Private', 'abbreviation' => 'PVT', 'paygrade' => 'E-1', 'description' => null, 'category' => 'Enlisted'],
            ],
            'statuses' => [['name' => 'Active', 'color' => '#22c55e']],
            'groups' => [
                ['name' => 'Airborne', 'description' => null, 'units' => [
                    ['name' => '1st Platoon', 'description' => null, 'slots' => [
                        ['name' => 'Rifleman 1', 'position' => 'Rifleman', 'specialty' => 'Infantryman'],
                    ]],
                ]],
            ],
            'awards' => [['name' => 'Bronze Star', 'description' => null, 'category' => 'Valor']],
            'qualifications' => [['name' => 'Parachutist', 'description' => null, 'category' => null]],
            'credentials' => [['name' => 'Jump Certification', 'description' => null, 'type' => 'certification', 'issuer' => 'Jump School']],
            'forms' => [['name' => 'Enlistment Application', 'description' => null, 'fields' => [
                ['name' => 'Callsign', 'type' => 'text', 'required' => true, 'options' => []],
            ]]],
        ];
    }

    public function test_generate_returns_a_blueprint_from_the_agents_structured_output(): void
    {
        OrganizationBlueprintAgent::fake([self::structuredResponse()]);

        $blueprint = OrganizationBlueprintService::generate('A WWII airborne milsim unit.');

        $this->assertSame('Easy Company', $blueprint->name);
        $this->assertSame(['Captain', 'Sergeant', 'Private'], collect($blueprint->ranks)->pluck('name')->all());
        $this->assertSame('1st Platoon', $blueprint->groups[0]->units[0]->name);
        $this->assertSame('Rifleman', $blueprint->groups[0]->units[0]->slots[0]->position);

        OrganizationBlueprintAgent::assertPrompted(fn (AgentPrompt $prompt): bool => $prompt->contains('A WWII airborne milsim unit.'));
    }

    public function test_generate_includes_the_chosen_preset_as_a_starting_point(): void
    {
        OrganizationBlueprintAgent::fake([self::structuredResponse()]);

        $preset = OrganizationBlueprintData::fromPreset('military');

        OrganizationBlueprintService::generate('A WWII airborne milsim unit.', 'military');

        OrganizationBlueprintAgent::assertPrompted(fn (AgentPrompt $prompt): bool => $prompt->contains('starting point')
            && $prompt->contains($preset->ranks[0]->name));
    }

    public function test_refine_sends_the_current_blueprint_with_the_instruction(): void
    {
        OrganizationBlueprintAgent::fake([self::structuredResponse()]);

        $current = new OrganizationBlueprintData(name: 'Draft', ranks: [new RankData(name: 'Corporal')]);

        $blueprint = OrganizationBlueprintService::refine($current, 'Add a captain rank.');

        $this->assertSame('Easy Company', $blueprint->name);

        OrganizationBlueprintAgent::assertPrompted(fn (AgentPrompt $prompt): bool => $prompt->contains('Corporal')
            && $prompt->contains('Add a captain rank.'));
    }

    public function test_extend_only_returns_entities_that_do_not_already_exist(): void
    {
        Rank::factory()->createQuietly(['name' => 'Captain']);
        $group = Group::factory()->createQuietly(['name' => 'Airborne']);
        $unit = Unit::factory()->createQuietly(['name' => '1st Platoon']);
        $group->units()->attach($unit);

        OrganizationBlueprintAgent::fake([[
            ...self::structuredResponse(),
            'groups' => [
                ['name' => 'Airborne', 'description' => null, 'units' => [
                    ['name' => '1st Platoon', 'description' => null, 'slots' => []],
                    ['name' => 'Medical Platoon', 'description' => null, 'slots' => []],
                ]],
            ],
        ]]);

        $blueprint = OrganizationBlueprintService::extend('Add a medical platoon.');

        $this->assertSame(['Sergeant', 'Private'], collect($blueprint->ranks)->pluck('name')->all());
        $this->assertCount(1, $blueprint->groups);
        $this->assertSame(['Medical Platoon'], collect($blueprint->groups[0]->units)->pluck('name')->all());

        OrganizationBlueprintAgent::assertPrompted(fn (AgentPrompt $prompt): bool => $prompt->contains('Captain')
            && $prompt->contains('Airborne (1st Platoon)')
            && $prompt->contains('Add a medical platoon.'));
    }

    public function test_it_refuses_to_prompt_once_the_monthly_limit_is_reached(): void
    {
        OrganizationBlueprintAgent::fake();

        AiUsage::factory()->createQuietly(['input_tokens' => 100000, 'output_tokens' => 0]);

        try {
            OrganizationBlueprintService::generate('Anything.');
            $this->fail('Expected the AI usage limit exception.');
        } catch (AiUsageLimitExceededException) {
            OrganizationBlueprintAgent::assertNeverPrompted();
        }
    }

    public function test_to_blueprint_discards_entries_without_a_name(): void
    {
        $blueprint = OrganizationBlueprintService::toBlueprint([
            'ranks' => [['name' => 'Sergeant'], ['name' => ''], ['abbreviation' => 'PVT']],
            'groups' => [['name' => 'Airborne', 'units' => [['name' => ''], ['name' => '2nd Platoon', 'slots' => [['name' => null]]]]]],
            'forms' => [['name' => 'Enlistment', 'fields' => [['name' => 'Age', 'type' => 'number'], ['name' => '']]]],
        ]);

        $this->assertSame(['Sergeant'], collect($blueprint->ranks)->pluck('name')->all());
        $this->assertSame(['2nd Platoon'], collect($blueprint->groups[0]->units)->pluck('name')->all());
        $this->assertSame([], $blueprint->groups[0]->units[0]->slots);
        $this->assertSame(['Age'], collect($blueprint->forms[0]->fields)->pluck('name')->all());
    }
}
