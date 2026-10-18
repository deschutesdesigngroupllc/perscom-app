<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Mcp\Tools;

use App\Models\Rank;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Tests\Feature\Tenant\Mcp\McpTestCase;

class GetResourceToolTest extends McpTestCase
{
    public function test_it_returns_the_same_record_as_the_api(): void
    {
        $rank = Rank::factory()->createQuietly();
        $token = $this->apiKey(['view:rank']);

        $result = $this->toolJson($this->callTool('get_resource', [
            'resource' => 'ranks',
            'id' => $rank->getKey(),
        ], $token));

        URL::forceRootUrl(config('api.url').'/'.config('api.version'));

        $api = $this->withToken($token)->getJson('/ranks/'.$rank->getKey())->assertOk()->json();

        $this->assertSame($api, $result);
        $this->assertSame($rank->name, $result['data']['name']);
    }

    public function test_it_expands_includes(): void
    {
        $rank = Rank::factory()->createQuietly();
        $user = User::factory()->createQuietly(['rank_id' => $rank->getKey()]);

        $result = $this->toolJson($this->callTool('get_resource', [
            'resource' => 'users',
            'id' => $user->getKey(),
            'include' => ['rank'],
        ], $this->apiKey(['view:user'])));

        $this->assertSame($rank->name, $result['data']['rank']['name']);
    }

    public function test_it_reports_a_missing_scope(): void
    {
        $user = User::factory()->createQuietly();

        $error = $this->toolError($this->callTool('get_resource', [
            'resource' => 'users',
            'id' => $user->getKey(),
        ], $this->apiKey(['view:rank'])));

        $this->assertStringContainsString('view:user', $error);
    }

    public function test_it_reports_a_missing_record(): void
    {
        $error = $this->toolError($this->callTool('get_resource', [
            'resource' => 'ranks',
            'id' => 999999,
        ], $this->apiKey(['view:rank'])));

        $this->assertStringContainsString('could not be found', $error);
    }

    public function test_it_rejects_an_unknown_resource(): void
    {
        $error = $this->toolError($this->callTool('get_resource', [
            'resource' => 'passwords',
            'id' => 1,
        ], $this->apiKey(['view:user'])));

        $this->assertStringContainsString('list_resources', $error);
    }
}
