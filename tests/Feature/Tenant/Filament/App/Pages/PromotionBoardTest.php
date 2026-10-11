<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages;

use App\Filament\App\Pages\PromotionBoard;
use App\Models\Enums\RankRecordType;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\RankRequirement;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class PromotionBoardTest extends TenantTestCase
{
    protected Rank $senior;

    protected Rank $junior;

    protected User $eligible;

    protected User $notEligible;

    protected function setUp(): void
    {
        parent::setUp();

        $this->senior = Rank::factory()->createQuietly(['name' => 'Sergeant', 'order' => 1]);
        $this->junior = Rank::factory()->createQuietly(['name' => 'Corporal', 'order' => 2]);

        RankRequirement::factory()->createQuietly(['rank_id' => $this->senior->getKey(), 'min_days_in_service' => 30]);

        $this->eligible = User::factory()->createQuietly(['rank_id' => $this->junior->getKey(), 'created_at' => now()->subDays(60)]);
        $this->notEligible = User::factory()->createQuietly(['rank_id' => $this->junior->getKey(), 'created_at' => now()->subDays(5)]);
    }

    public function test_eligible_tab_lists_only_eligible_members(): void
    {
        $this->actingAsSuperAdmin();

        Livewire::test(PromotionBoard::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$this->eligible])
            ->assertCanNotSeeTableRecords([$this->notEligible]);
    }

    public function test_not_yet_eligible_tab_lists_missing_requirements(): void
    {
        $this->actingAsSuperAdmin();

        Livewire::test(PromotionBoard::class)
            ->set('activeTab', 'not_yet_eligible')
            ->assertCanSeeTableRecords([$this->notEligible])
            ->assertCanNotSeeTableRecords([$this->eligible])
            ->assertSee('Time in service: 5 days (30 required)');
    }

    public function test_promote_action_creates_a_promotion_rank_record(): void
    {
        Notification::fake();

        $admin = $this->actingAsSuperAdmin();

        Livewire::test(PromotionBoard::class)
            ->callAction(TestAction::make('promote')->table($this->eligible), ['text' => 'Well earned.'])
            ->assertHasNoErrors()
            ->assertNotified();

        $this->assertDatabaseHas('records_ranks', [
            'user_id' => $this->eligible->getKey(),
            'rank_id' => $this->senior->getKey(),
            'author_id' => $admin->getKey(),
            'type' => RankRecordType::PROMOTION->value,
            'text' => 'Well earned.',
        ]);
        $this->assertSame($this->senior->getKey(), $this->eligible->fresh()->rank_id);
    }

    public function test_bulk_promote_skips_members_who_are_not_eligible(): void
    {
        Notification::fake();

        $this->actingAsSuperAdmin();

        $page = Livewire::test(PromotionBoard::class)->instance();
        $this->assertInstanceOf(PromotionBoard::class, $page);

        $count = $page->promote(new Collection([$this->eligible, $this->notEligible]));

        $this->assertSame(1, $count);
        $this->assertSame(1, RankRecord::query()->where('rank_id', $this->senior->getKey())->count());
        $this->assertSame($this->junior->getKey(), $this->notEligible->fresh()->rank_id);
    }

    public function test_members_without_rank_record_permissions_cannot_access_the_board(): void
    {
        $this->actingAs(User::factory()->createQuietly());

        $this->assertFalse(PromotionBoard::canAccess());

        Livewire::test(PromotionBoard::class)->assertForbidden();
    }

    protected function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
