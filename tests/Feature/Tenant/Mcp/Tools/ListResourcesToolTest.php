<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Mcp\Tools;

use App\Http\Controllers\Api\Users\UsersController;
use App\Mcp\ApiResourceRegistry;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use Tests\Feature\Tenant\Mcp\McpTestCase;

class ListResourcesToolTest extends McpTestCase
{
    public function test_it_lists_every_registered_resource_with_its_capabilities(): void
    {
        $result = $this->toolJson($this->callTool('list_resources', [], $this->apiKey(['view:user'])));

        $resources = collect($result['resources'])->keyBy('resource');

        $this->assertSame(ApiResourceRegistry::keys(), $resources->keys()->all());

        $users = $resources->get('users');
        $controller = new ReflectionClass(UsersController::class)->newInstanceWithoutConstructor();

        $this->assertSame('view:user', $users['required_scope']);
        $this->assertSame(array_values($controller->filterableBy()), $users['filterable']);
        $this->assertSame(array_values($controller->includes()), $users['includes']);
    }

    public function test_it_describes_a_single_resource(): void
    {
        $result = $this->toolJson($this->callTool('list_resources', ['resource' => 'ranks'], $this->apiKey(['view:rank'])));

        $this->assertCount(1, $result['resources']);
        $this->assertSame('ranks', $result['resources'][0]['resource']);
    }

    public function test_every_registered_resource_has_a_search_route_in_the_api(): void
    {
        collect(ApiResourceRegistry::all())->each(function (array $resource): void {
            $this->assertTrue(
                Route::has(sprintf('api.%s.search', $resource['endpoint'])),
                sprintf('The API does not register a search route for [%s].', $resource['endpoint']),
            );
        });
    }
}
