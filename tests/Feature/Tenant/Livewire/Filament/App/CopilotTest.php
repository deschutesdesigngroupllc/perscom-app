<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Livewire\Filament\App;

use App\Ai\Agents\CommandCopilot;
use App\Data\Copilot\ProposedChangeSetData;
use App\Data\Copilot\ProposedOperationData;
use App\Livewire\Filament\App\Copilot;
use App\Models\AiUsage;
use App\Models\Award;
use App\Models\Enums\CopilotOperationType;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class CopilotTest extends TenantTestCase
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

    public function test_the_ask_action_is_hidden_without_a_provider_key(): void
    {
        config(['ai.providers.anthropic.key' => null]);
        $this->actingAs($this->superAdmin());

        Livewire::test(Copilot::class)
            ->assertDontSee('Ask PERSCOM');
    }

    public function test_the_ask_action_is_hidden_once_the_monthly_limit_is_reached(): void
    {
        AiUsage::factory()->createQuietly(['input_tokens' => 100000, 'output_tokens' => 0]);
        $this->actingAs($this->superAdmin());

        Livewire::test(Copilot::class)
            ->assertDontSee('Ask PERSCOM');
    }

    public function test_asking_shows_the_answer_for_review(): void
    {
        CommandCopilot::fake(['You have **3** members eligible.']);
        $this->actingAs($this->superAdmin());

        Livewire::test(Copilot::class)
            ->assertSee('Ask PERSCOM')
            ->callAction('ask', ['prompt' => 'Who is eligible for promotion?'])
            ->assertSet('answer', 'You have **3** members eligible.')
            ->assertActionMounted('review');
    }

    public function test_the_review_shows_proposed_changes_and_escapes_html_in_the_answer(): void
    {
        $operation = new ProposedOperationData(CopilotOperationType::AWARD_RECORD, [1, 2], 1, summary: 'Award the Bronze Star to 2 members');

        $this->view('livewire.filament.app.copilot-review', [
            'answer' => 'Proposed **one** award. <script>alert(1)</script>',
            'operations' => [$operation],
        ])
            ->assertSee('<strong>one</strong>', false)
            ->assertDontSee('<script>', false)
            ->assertSee('Award the Bronze Star to 2 members')
            ->assertSee('2 members');
    }

    public function test_applying_changes_creates_the_proposed_records(): void
    {
        $actor = $this->superAdmin();
        $member = User::factory()->createQuietly();
        $award = Award::factory()->createQuietly();
        $this->actingAs($actor);

        $changeSet = new ProposedChangeSetData([
            new ProposedOperationData(CopilotOperationType::AWARD_RECORD, [$member->getKey()], $award->getKey(), summary: 'Award the medal'),
        ]);

        Livewire::test(Copilot::class)
            ->set('answer', 'Proposed one award.')
            ->set('changeSet', $changeSet->toArray())
            ->callAction('applyChanges')
            ->assertNotified('Changes Applied')
            ->assertSet('changeSet', []);

        $this->assertDatabaseHas('records_awards', ['user_id' => $member->getKey(), 'award_id' => $award->getKey()]);
    }

    public function test_applying_unauthorized_changes_shows_an_error_and_writes_nothing(): void
    {
        $actor = User::factory()->createQuietly();
        $member = User::factory()->createQuietly();
        $award = Award::factory()->createQuietly();
        $this->actingAs($actor);

        $changeSet = new ProposedChangeSetData([
            new ProposedOperationData(CopilotOperationType::AWARD_RECORD, [$member->getKey()], $award->getKey(), summary: 'Award the medal'),
        ]);

        Livewire::test(Copilot::class)
            ->set('changeSet', $changeSet->toArray())
            ->callAction('applyChanges')
            ->assertNotified('Not Allowed');

        $this->assertDatabaseMissing('records_awards', ['user_id' => $member->getKey()]);
    }

    private function superAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        return $user;
    }
}
