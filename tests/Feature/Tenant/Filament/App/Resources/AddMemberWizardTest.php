<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\UserResource;
use App\Filament\App\Resources\UserResource\Pages\CreateUser;
use App\Models\Enums\InviteType;
use App\Models\Invite;
use App\Models\Rank;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\User\Invited;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class AddMemberWizardTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_adding_a_member_starts_their_history_and_sends_an_invite(): void
    {
        Notification::fake();

        $unit = Unit::factory()->createQuietly();
        $rank = Rank::factory()->createQuietly();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Jordan Reyes',
                'email' => 'jordan.reyes@example.test',
                'send_invite' => true,
                'unit_id' => $unit->getKey(),
                'rank_id' => $rank->getKey(),
            ])
            ->call('create')
            ->assertHasNoErrors();

        $member = User::query()->where('email', 'jordan.reyes@example.test')->firstOrFail();

        $this->assertTrue($member->approved);
        $this->assertSame($rank->getKey(), $member->rank_id);
        $this->assertSame($unit->getKey(), $member->unit_id);
        $this->assertDatabaseHas('records_ranks', ['user_id' => $member->getKey(), 'rank_id' => $rank->getKey()]);
        $this->assertDatabaseHas('records_assignments', ['user_id' => $member->getKey(), 'unit_id' => $unit->getKey()]);
        $this->assertTrue(Invite::query()->pending()->where('user_id', $member->getKey())->where('type', InviteType::CLAIM)->exists());
        Notification::assertSentOnDemand(Invited::class);
    }

    public function test_placement_is_optional_and_no_invite_is_sent_when_unchecked(): void
    {
        Notification::fake();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Sam Ortiz',
                'email' => 'sam.ortiz@example.test',
                'send_invite' => false,
            ])
            ->call('create')
            ->assertHasNoErrors()
            ->assertRedirect(UserResource::getUrl('view', ['record' => User::query()->where('email', 'sam.ortiz@example.test')->firstOrFail()]));

        Notification::assertNothingSent();
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->createQuietly(['email' => 'taken@example.test']);

        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'Someone', 'email' => 'taken@example.test'])
            ->call('create')
            ->assertHasFormErrors(['email' => 'unique']);
    }
}
