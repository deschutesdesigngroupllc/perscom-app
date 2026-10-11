<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Mcp\Tools;

use App\Models\User;
use Tests\Feature\Tenant\Mcp\McpTestCase;

class GetCurrentUserToolTest extends McpTestCase
{
    public function test_it_returns_the_signed_in_member(): void
    {
        $user = User::factory()->createQuietly();

        $result = $this->toolJson($this->callTool('get_current_user', [], $this->apiKey(['view:user'], $user)));

        $this->assertSame($user->getKey(), $result['data']['id']);
        $this->assertSame($user->email, $result['data']['email']);
    }
}
