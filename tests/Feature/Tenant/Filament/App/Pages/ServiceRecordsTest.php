<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages;

use App\Filament\App\Pages\PromotionBoard;
use App\Filament\App\Pages\Readiness;
use App\Filament\App\Pages\ServiceRecords;
use App\Filament\App\Resources\AwardRecordResource;
use App\Filament\App\Resources\RankRecordResource;
use App\Filament\App\Resources\SubmissionResource;
use App\Models\AwardRecord;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class ServiceRecordsTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('app');

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_it_summarizes_each_record_type_with_links(): void
    {
        AwardRecord::factory()->count(2)->createQuietly(['created_at' => now()->subDays(3)]);

        $types = collect((new ServiceRecords)->getRecordTypes())->keyBy('label');

        $this->assertCount(count(ServiceRecords::RECORD_RESOURCES), $types);
        $this->assertGreaterThanOrEqual(2, $types[AwardRecordResource::getTitleCasePluralModelLabel()]['recent']);

        Livewire::test(ServiceRecords::class)
            ->assertSee(AwardRecordResource::getTitleCasePluralModelLabel())
            ->assertSee(AwardRecordResource::getUrl('index'));
    }

    public function test_detail_pages_nest_under_their_parents_in_the_sidebar(): void
    {
        $this->assertSame('Service Records', RankRecordResource::getNavigationParentItem());
        $this->assertSame('Forms', SubmissionResource::getNavigationParentItem());
        $this->assertSame('Members', PromotionBoard::getNavigationParentItem());
        $this->assertSame('Members', Readiness::getNavigationParentItem());
    }

    public function test_members_without_record_access_cannot_open_it(): void
    {
        $this->actingAs(User::factory()->withoutRoles()->createQuietly());

        $this->assertFalse(ServiceRecords::canAccess());
    }
}
