<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Mcp\Tools;

use App\Models\Rank;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Tests\Feature\Tenant\Mcp\McpTestCase;

class SearchResourceToolTest extends McpTestCase
{
    public function test_it_returns_the_same_results_as_the_api_search_endpoint(): void
    {
        Rank::factory()->count(3)->createQuietly();
        $target = Rank::factory()->createQuietly(['name' => 'Master Sergeant']);
        $token = $this->apiKey(['view:rank']);

        $result = $this->toolJson($this->callTool('search_resource', [
            'resource' => 'ranks',
            'filters' => [['field' => 'name', 'value' => 'Master Sergeant']],
        ], $token));

        URL::forceRootUrl(config('api.url').'/'.config('api.version'));

        $api = $this->withToken($token)->postJson('/ranks/search?limit=25', [
            'filters' => [['field' => 'name', 'operator' => '=', 'value' => 'Master Sergeant']],
        ])->assertOk()->json();

        $this->assertSame($api, $result);
        $this->assertCount(1, $result['data']);
        $this->assertSame($target->getKey(), $result['data'][0]['id']);
    }

    public function test_it_sorts_and_paginates(): void
    {
        Rank::factory()->createQuietly(['name' => 'Alpha']);
        Rank::factory()->createQuietly(['name' => 'Bravo']);
        Rank::factory()->createQuietly(['name' => 'Charlie']);

        $result = $this->toolJson($this->callTool('search_resource', [
            'resource' => 'ranks',
            'sort' => [['field' => 'name', 'direction' => 'desc']],
            'limit' => 2,
        ], $this->apiKey(['view:rank'])));

        $this->assertSame(['Charlie', 'Bravo'], collect($result['data'])->pluck('name')->all());
        $this->assertSame(2, $result['meta']['per_page']);
    }

    public function test_a_token_without_the_resource_scope_is_refused(): void
    {
        User::factory()->createQuietly();

        $error = $this->toolError($this->callTool('search_resource', [
            'resource' => 'users',
        ], $this->apiKey(['view:rank'])));

        $this->assertStringContainsString('view:user', $error);
    }

    public function test_the_api_rejects_a_field_that_is_not_filterable(): void
    {
        $error = $this->toolError($this->callTool('search_resource', [
            'resource' => 'users',
            'filters' => [['field' => 'password', 'value' => 'secret']],
        ], $this->apiKey(['view:user'])));

        $this->assertStringContainsString('The request was invalid.', $error);
    }

    public function test_it_rejects_an_unknown_resource(): void
    {
        $error = $this->toolError($this->callTool('search_resource', [
            'resource' => 'oauth-clients',
        ], $this->apiKey(['view:user'])));

        $this->assertStringContainsString('list_resources', $error);
    }
}
