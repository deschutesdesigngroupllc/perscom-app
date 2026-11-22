<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\AwardRecordResource\Pages\CreateAwardRecord;
use App\Filament\App\Resources\UserResource\Pages\ViewUser;
use App\Filament\App\Resources\UserResource\RelationManagers\AwardRecordsRelationManager;
use App\Models\AwardRecord;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class RecordEffectiveDateTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);
    }

    public function test_record_forms_call_the_record_date_the_effective_date(): void
    {
        Livewire::test(CreateAwardRecord::class)
            ->assertSee('Effective Date')
            ->assertSee('The date this took effect.');
    }

    public function test_profile_tabs_show_record_dates_as_absolute_dates(): void
    {
        $member = User::factory()->createQuietly();
        AwardRecord::factory()->for($member)->createQuietly(['created_at' => '2024-01-15 12:00:00']);

        Livewire::test(AwardRecordsRelationManager::class, ['ownerRecord' => $member, 'pageClass' => ViewUser::class])
            ->assertSee('Jan 15, 2024')
            ->assertDontSee('years ago');
    }
}
