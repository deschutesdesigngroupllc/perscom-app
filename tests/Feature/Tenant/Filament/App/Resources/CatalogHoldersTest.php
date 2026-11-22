<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\AwardResource\Pages\EditAward;
use App\Filament\App\Resources\AwardResource\RelationManagers\HoldersRelationManager as AwardHolders;
use App\Filament\App\Resources\QualificationResource\Pages\EditQualification;
use App\Filament\App\Resources\QualificationResource\RelationManagers\HoldersRelationManager as QualificationHolders;
use App\Filament\App\Resources\RankResource\Pages\EditRank;
use App\Filament\App\Resources\RankResource\RelationManagers\HoldersRelationManager as RankHolders;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\Rank;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class CatalogHoldersTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_an_award_lists_who_received_it(): void
    {
        $award = Award::factory()->createQuietly();
        $holder = AwardRecord::factory()->for($award)->createQuietly();
        $other = AwardRecord::factory()->createQuietly();

        Livewire::test(AwardHolders::class, ['ownerRecord' => $award, 'pageClass' => EditAward::class])
            ->assertCanSeeTableRecords([$holder])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_a_qualification_lists_who_holds_it(): void
    {
        $qualification = Qualification::factory()->createQuietly();
        $holder = QualificationRecord::factory()->for($qualification)->createQuietly();

        Livewire::test(QualificationHolders::class, ['ownerRecord' => $qualification, 'pageClass' => EditQualification::class])
            ->assertCanSeeTableRecords([$holder]);
    }

    public function test_a_rank_lists_members_currently_at_that_rank(): void
    {
        $rank = Rank::factory()->createQuietly();
        $holder = User::factory()->createQuietly(['rank_id' => $rank->getKey()]);
        $other = User::factory()->createQuietly(['rank_id' => null]);

        Livewire::test(RankHolders::class, ['ownerRecord' => $rank, 'pageClass' => EditRank::class])
            ->assertCanSeeTableRecords([$holder])
            ->assertCanNotSeeTableRecords([$other]);
    }
}
