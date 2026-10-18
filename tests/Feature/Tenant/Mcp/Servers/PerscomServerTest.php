<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Mcp\Servers;

use App\Http\Controllers\Mcp\ProtectedResourceMetadataController;
use App\Mcp\ApiResourceRegistry;
use App\Models\Rank;
use App\Models\Unit;
use App\Settings\OrganizationSettings;
use Illuminate\Support\Str;
use Laravel\Passport\Token;
use Tests\Feature\Tenant\Mcp\McpTestCase;

class PerscomServerTest extends McpTestCase
{
    public function test_unauthenticated_requests_are_rejected_with_discovery_metadata(): void
    {
        $response = $this->mcp('tools/list');

        $response->assertUnauthorized();

        $this->assertStringContainsString(
            'resource_metadata="'.$this->tenant->url.'/.well-known/oauth-protected-resource/mcp"',
            (string) $response->headers->get('WWW-Authenticate'),
        );
    }

    public function test_an_invalid_token_is_rejected(): void
    {
        $this->mcp('tools/list', [], 'not-a-real-token')->assertUnauthorized();
    }

    public function test_a_revoked_token_is_rejected(): void
    {
        $token = $this->apiKey(['view:user']);

        Token::query()->update(['revoked' => true]);

        $this->mcp('tools/list', [], $token)->assertUnauthorized();
    }

    public function test_every_tool_is_marked_read_only(): void
    {
        $tools = collect($this->mcp('tools/list', [], $this->apiKey(['view:user']))->assertOk()->json('result.tools'));

        $this->assertEqualsCanonicalizing(
            ['list_resources', 'search_resource', 'get_resource', 'get_current_user'],
            $tools->pluck('name')->all(),
        );

        $tools->each(function (array $tool): void {
            $this->assertTrue($tool['annotations']['readOnlyHint'], sprintf('[%s] is not marked read-only.', $tool['name']));
            $this->assertFalse($tool['annotations']['openWorldHint']);
        });
    }

    public function test_the_organization_structure_resource_lists_ranks_units_and_positions(): void
    {
        $senior = Rank::factory()->createQuietly(['name' => 'Colonel', 'order' => 1]);
        $junior = Rank::factory()->createQuietly(['name' => 'Private', 'order' => 2]);
        $unit = Unit::factory()->createQuietly();

        $response = $this->mcp('resources/read', ['uri' => 'perscom://organization/structure'], $this->apiKey(['view:rank', 'view:unit']))
            ->assertOk();

        $structure = json_decode((string) $response->json('result.contents.0.text'), true);

        $this->assertSame([$senior->name, $junior->name], collect($structure['ranks'])->pluck('name')->take(2)->all());
        $this->assertContains($unit->name, collect($structure['units'])->pluck('name')->all());
        $this->assertArrayNotHasKey('positions', $structure, 'Positions require the view:position scope and should be omitted.');
    }

    public function test_the_protected_resource_metadata_points_at_the_tenant(): void
    {
        $this->getJson($this->tenant->url.'/.well-known/oauth-protected-resource/mcp')
            ->assertOk()
            ->assertJsonPath('resource', $this->tenant->url.'/mcp')
            ->assertJsonPath('authorization_servers', [Str::lower($this->tenant->url)])
            ->assertJsonPath('scopes_supported', ApiResourceRegistry::scopes());
    }

    public function test_the_protected_resource_metadata_uses_the_organization_name_without_a_tenant(): void
    {
        OrganizationSettings::fake(['name' => 'Single Database Org', 'email' => 'org@example.com', 'timezone' => 'UTC']);

        tenancy()->end();

        try {
            $response = resolve(ProtectedResourceMetadataController::class)();
        } finally {
            tenancy()->initialize($this->tenant);
        }

        $this->assertSame('Single Database Org PERSCOM', $response->getData(true)['resource_name']);
        $this->assertSame(ApiResourceRegistry::scopes(), $response->getData(true)['scopes_supported']);
    }

    public function test_the_authorization_server_metadata_uses_the_tenant_passport_endpoints(): void
    {
        $this->getJson($this->tenant->url.'/.well-known/oauth-authorization-server')
            ->assertOk()
            ->assertJsonPath('issuer', Str::lower($this->tenant->url))
            ->assertJsonPath('authorization_endpoint', $this->tenant->url.'/oauth/authorize')
            ->assertJsonPath('token_endpoint', $this->tenant->url.'/oauth/token')
            ->assertJsonPath('registration_endpoint', $this->tenant->url.'/oauth/register')
            ->assertJsonPath('code_challenge_methods_supported', ['S256']);
    }

    public function test_mcp_clients_can_register_dynamically(): void
    {
        $this->postJson($this->tenant->url.'/oauth/register', [
            'client_name' => 'Claude',
            'redirect_uris' => ['https://claude.ai/api/mcp/auth_callback'],
        ])
            ->assertCreated()
            ->assertJsonPath('token_endpoint_auth_method', 'none');

        $this->assertDatabaseHas('oauth_clients', ['name' => 'Claude'], 'tenant');
    }
}
