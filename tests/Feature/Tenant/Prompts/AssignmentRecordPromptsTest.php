<?php

namespace Tests\Feature\Tenant\Prompts;

use App\Jobs\GenerateOpenAiNewsfeedContent;
use App\Models\AssignmentRecord;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class AssignmentRecordPromptsTest extends TenantTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Queue::fake([GenerateOpenAiNewsfeedContent::class]);
    }

    public function test_created_headline_prompt_string_is_properly_substituted()
    {
        $record = AssignmentRecord::factory()->create();
        $prompt = $record->generatePromptForEvent('created');

        $this->assertStringContainsString($record->user->name, $prompt);
        $this->assertStringContainsString($record->text, $prompt);
        $this->assertStringContainsString($record->unit->name, $prompt);
        $this->assertStringContainsString($record->unit->description, $prompt);
        $this->assertStringContainsString($record->position->name, $prompt);
        $this->assertStringContainsString($record->position->description, $prompt);
        $this->assertStringContainsString($record->specialty->name, $prompt);
        $this->assertStringContainsString($record->specialty->description, $prompt);
    }

    public function test_created_text_prompt_string_is_properly_substituted()
    {
        $record = AssignmentRecord::factory()->create();
        $prompt = $record->generatePromptForEvent('created', 'text');

        $this->assertStringContainsString($record->user->name, $prompt);
        $this->assertStringContainsString($record->text, $prompt);
        $this->assertStringContainsString($record->unit->name, $prompt);
        $this->assertStringContainsString($record->unit->description, $prompt);
        $this->assertStringContainsString($record->position->name, $prompt);
        $this->assertStringContainsString($record->position->description, $prompt);
        $this->assertStringContainsString($record->specialty->name, $prompt);
        $this->assertStringContainsString($record->specialty->description, $prompt);
    }
}
