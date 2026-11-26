<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\SubmissionResource;
use App\Filament\App\Resources\SubmissionResource\Pages\ListSubmissions;
use App\Filament\App\Resources\SubmissionResource\Pages\ViewSubmission;
use App\Models\Status;
use App\Models\Submission;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class SubmissionReviewTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_status_can_be_changed_from_the_submission_page(): void
    {
        $submission = Submission::factory()->createQuietly();
        $approved = Status::factory()->createQuietly(['name' => 'Approved']);

        Livewire::test(ViewSubmission::class, ['record' => $submission->getKey()])
            ->callAction('changeStatus', ['status_id' => $approved->getKey(), 'text' => 'Welcome aboard.'])
            ->assertHasNoActionErrors()
            ->assertNotified('Status Updated');

        $this->assertTrue($submission->refresh()->status?->is($approved));
        $this->assertDatabaseHas('model_has_statuses', ['model_id' => $submission->getKey(), 'status_id' => $approved->getKey(), 'text' => 'Welcome aboard.']);
    }

    public function test_next_unread_links_to_the_oldest_other_unread_submission(): void
    {
        Submission::query()->update(['read_at' => now()]);

        $current = Submission::factory()->createQuietly(['read_at' => null]);
        $oldest = Submission::factory()->createQuietly(['read_at' => null, 'created_at' => now()->subDays(3)]);

        Livewire::test(ViewSubmission::class, ['record' => $current->getKey()])
            ->assertActionVisible('nextUnread')
            ->assertActionHasUrl('nextUnread', SubmissionResource::getUrl('view', ['record' => $oldest]));
    }

    public function test_the_status_filter_matches_the_current_status_only(): void
    {
        $pending = Status::factory()->createQuietly(['name' => 'Pending']);
        $approved = Status::factory()->createQuietly(['name' => 'Approved']);

        $nowApproved = Submission::factory()->createQuietly();
        $nowApproved->statuses()->attach($pending, ['created_at' => now()->subDay(), 'updated_at' => now()->subDay()]);
        $nowApproved->statuses()->attach($approved, ['created_at' => now(), 'updated_at' => now()]);

        $stillPending = Submission::factory()->createQuietly();
        $stillPending->statuses()->attach($pending);

        Livewire::test(ListSubmissions::class)
            ->filterTable('status', [$pending->getKey()])
            ->assertCanSeeTableRecords([$stillPending])
            ->assertCanNotSeeTableRecords([$nowApproved]);
    }
}
