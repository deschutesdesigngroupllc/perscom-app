<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings\Pages\ConnectAiTools;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class ConnectAiToolsTest extends TenantTestCase
{
    public function test_super_admins_see_the_mcp_server_url_and_client_snippets(): void
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        Livewire::test(ConnectAiTools::class)
            ->assertSuccessful()
            ->assertSee(route('mcp.server'))
            ->assertSee('claude mcp add --transport http perscom')
            ->assertSee('read-only')
            ->assertSeeInOrder(['Claude', 'Claude Code', 'ChatGPT', 'Gemini', 'Grok', 'Cursor', 'VS Code', 'Other Tools'])
            ->assertSee('Developer Mode')
            ->assertSee('httpUrl')
            ->assertSee('grok.com/connectors');
    }

    public function test_members_without_the_super_admin_role_cannot_access_the_page(): void
    {
        $this->actingAs(User::factory()->createQuietly());

        $this->assertFalse(ConnectAiTools::canAccess());
    }
}
