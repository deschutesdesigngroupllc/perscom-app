<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\Public\Pages;

use App\Filament\Public\Pages\Forms\Submit;
use App\Models\Form;
use App\Models\Submission;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class PublicFormSubmitTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('public');
        config(['services.cloudflare.turnstile.site_key' => null]);
    }

    public function test_a_public_form_can_be_submitted_and_names_itself_on_the_button(): void
    {
        $form = Form::factory()->createQuietly(['name' => 'Enlistment Application', 'slug' => 'enlist-'.uniqid(), 'is_public' => true]);

        Livewire::test(Submit::class, ['slug' => $form->slug])
            ->assertSee('Submit Enlistment Application')
            ->call('submit')
            ->assertNotified('Form Submitted');

        $this->assertTrue(Submission::query()->where('form_id', $form->getKey())->exists());
    }

    public function test_repeated_submissions_are_rate_limited(): void
    {
        $form = Form::factory()->createQuietly(['slug' => 'limited-'.uniqid(), 'is_public' => true]);

        $component = Livewire::test(Submit::class, ['slug' => $form->slug]);

        foreach (range(1, Submit::SUBMISSIONS_PER_MINUTE) as $attempt) {
            $component->call('submit');
        }

        $component->call('submit')->assertNotified('Too Many Submissions');

        $this->assertSame(Submit::SUBMISSIONS_PER_MINUTE, Submission::query()->where('form_id', $form->getKey())->count());
    }
}
