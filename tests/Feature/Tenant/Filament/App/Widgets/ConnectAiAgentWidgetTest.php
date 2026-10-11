<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Widgets;

use App\Filament\App\Clusters\Settings\Pages\ConnectAiTools;
use App\Filament\App\Widgets\ConnectAiAgentWidget;
use App\Models\PassportToken;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class ConnectAiAgentWidgetTest extends TenantTestCase
{
    public function test_super_admin_sees_the_mcp_server_url_and_setup_link(): void
    {
        $this->actingAsSuperAdmin();

        $this->assertTrue(ConnectAiAgentWidget::canView());

        Livewire::test(ConnectAiAgentWidget::class)
            ->assertSee('Ask your AI about your personnel')
            ->assertSee(route('mcp.server'))
            ->assertSee(ConnectAiTools::getUrl());
    }

    public function test_members_without_access_to_connect_ai_tools_do_not_see_it(): void
    {
        $this->actingAs(User::factory()->createQuietly());

        $this->assertFalse(ConnectAiAgentWidget::canView());
    }

    public function test_dismissing_hides_it_for_that_user_only(): void
    {
        $user = $this->actingAsSuperAdmin();

        Livewire::test(ConnectAiAgentWidget::class)
            ->call('dismiss')
            ->assertRedirect();

        $this->assertFalse(ConnectAiAgentWidget::canView());

        $this->actingAsSuperAdmin();

        $this->assertTrue(ConnectAiAgentWidget::canView());
        $this->assertNotSame($user->getKey(), Auth::id());
    }

    public function test_it_hides_once_an_ai_tool_has_connected(): void
    {
        $user = $this->actingAsSuperAdmin();

        PassportToken::factory()->createQuietly(['user_id' => $user->getKey(), 'scopes' => ['mcp:use', 'view:user']]);

        $this->assertFalse(ConnectAiAgentWidget::canView());
    }

    public function test_revoked_ai_tool_connections_do_not_count(): void
    {
        $user = $this->actingAsSuperAdmin();

        PassportToken::factory()->createQuietly(['user_id' => $user->getKey(), 'scopes' => ['mcp:use'], 'revoked' => true]);
        PassportToken::factory()->createQuietly(['user_id' => $user->getKey(), 'scopes' => ['view:user']]);

        $this->assertTrue(ConnectAiAgentWidget::canView());
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
