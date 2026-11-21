<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Widgets;

use App\Filament\App\Resources\UserResource;
use App\Filament\App\Widgets\NeedsAttentionWidget;
use App\Models\Submission;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class NeedsAttentionWidgetTest extends TenantTestCase
{
    public function test_it_lists_outstanding_items_with_counts_for_admins(): void
    {
        $this->actingAsSuperAdmin();

        User::factory()->count(2)->createQuietly(['approved' => false]);
        Submission::factory()->count(3)->createQuietly(['read_at' => null]);

        $keys = collect((new NeedsAttentionWidget)->getItems())->pluck('count', 'key');

        $this->assertSame(2, $keys->get('approvals'));
        $this->assertSame('2', UserResource::getNavigationBadge());
        $this->assertGreaterThanOrEqual(3, $keys->get('submissions'));

        Livewire::test(NeedsAttentionWidget::class)
            ->assertSee('Awaiting Approval')
            ->assertSee('Unread Submissions')
            ->assertDontSee('All clear');
    }

    public function test_it_shows_all_clear_when_nothing_is_outstanding(): void
    {
        $this->actingAsSuperAdmin();

        User::query()->where('approved', false)->update(['approved' => true]);
        Submission::query()->whereNull('read_at')->update(['read_at' => now()]);

        Livewire::test(NeedsAttentionWidget::class)
            ->assertSee('All clear');
    }

    public function test_members_without_admin_permissions_do_not_see_it(): void
    {
        $this->actingAs(User::factory()->withoutRoles()->createQuietly());

        $this->assertFalse(NeedsAttentionWidget::canView());
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
