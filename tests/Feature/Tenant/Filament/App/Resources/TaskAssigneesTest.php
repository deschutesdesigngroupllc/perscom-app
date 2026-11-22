<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\TaskResource\Pages\EditTask;
use App\Filament\App\Resources\TaskResource\RelationManagers\AssigneesRelationManager;
use App\Models\Task;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class TaskAssigneesTest extends TenantTestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->createQuietly();
        $this->admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($this->admin);
    }

    public function test_members_can_be_assigned_with_a_due_date(): void
    {
        Notification::fake();

        $task = Task::factory()->createQuietly();
        $members = User::factory()->count(2)->createQuietly();

        Livewire::test(AssigneesRelationManager::class, ['ownerRecord' => $task, 'pageClass' => EditTask::class])
            ->callAction(TestAction::make('assignMembers')->table(), [
                'users' => $members->modelKeys(),
                'due_at' => now()->addWeek()->toDateTimeString(),
            ])
            ->assertHasNoActionErrors();

        $members->each(fn (User $member) => $this->assertDatabaseHas('users_tasks', [
            'task_id' => $task->getKey(),
            'user_id' => $member->getKey(),
            'assigned_by_id' => $this->admin->getKey(),
        ]));
    }

    public function test_an_assignee_can_be_marked_complete(): void
    {
        $task = Task::factory()->createQuietly();
        $member = User::factory()->createQuietly();
        $task->users()->attach($member, ['assigned_at' => now(), 'due_at' => now()->subDay()]);

        Livewire::test(AssigneesRelationManager::class, ['ownerRecord' => $task, 'pageClass' => EditTask::class])
            ->assertSee('Overdue')
            ->callAction(TestAction::make('markComplete')->table($member))
            ->assertHasNoActionErrors();

        $this->assertNotNull($task->users()->whereKey($member->getKey())->firstOrFail()->getRelationValue('assignment')->completed_at);
    }
}
