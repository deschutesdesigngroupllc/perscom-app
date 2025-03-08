<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\Contracts\ApiResourceTestContract;

abstract class ApiResourceTestCase extends ApiTestCase implements ApiResourceTestContract
{
    public $expectedIndexCount;

    protected Collection|Model $factory;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->user = User::factory()->unassigned()->createQuietly();
        $this->factory = $this->factory()->createQuietly();

        $beforeCallback = Str::camel('before_'.$this->name());
        if (method_exists($this, $beforeCallback)) {
            $this->$beforeCallback();
        }
    }

    public function test_can_reach_index_endpoint(): void
    {
        $this->withToken($this->apiKey($this->scopes()['index']))
            ->getJson("/{$this->endpoint()}")
            ->assertJsonCount($this->expectedIndexCount ?? 1, 'data')
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertSuccessful();
    }

    public function test_can_reach_show_endpoint(): void
    {
        $this->withToken($this->apiKey($this->scopes()['show']))
            ->getJson("/{$this->endpoint()}/{$this->factory->getKey()}")
            ->assertJsonStructure(['data'])
            ->assertSuccessful();
    }

    public function test_can_reach_store_endpoint(): void
    {
        $data = $this->storeData();

        $this->withToken($this->apiKey($this->scopes()['store']))
            ->postJson("/{$this->endpoint()}", $data)
            ->assertJsonStructure(['data'])
            ->assertSuccessful();

        if (method_exists($this, 'beforeAssertDatabaseHas')) {
            $this->beforeAssertDatabaseHas($data);
        }

        $this->assertDatabaseHas($this->getTable($this->model()), $data);
    }

    public function test_can_reach_update_endpoint(): void
    {
        $data = $this->updateData();

        $this->withToken($this->apiKey($this->scopes()['update']))
            ->patchJson("/{$this->endpoint()}/{$this->factory->getKey()}", $data)
            ->assertJsonStructure(['data'])
            ->assertSuccessful();

        if (method_exists($this, 'beforeAssertDatabaseHas')) {
            $this->beforeAssertDatabaseHas($data);
        }

        $this->assertDatabaseHas($this->getTable($this->model()), $data);
    }

    public function test_can_reach_delete_endpoint(): void
    {
        $this->withToken($this->apiKey($this->scopes()['delete']))
            ->deleteJson("/{$this->endpoint()}/{$this->factory->getKey()}")
            ->assertJsonStructure(['data'])
            ->assertSuccessful();

        $this->assertDatabaseMissing($this->getTable($this->model()), [
            'id' => $this->factory->getKey(),
        ]);
    }

    public function test_cannot_reach_index_endpoint_with_missing_scope(): void
    {
        $class = class_basename($this->model());

        if (Str::contains($class, 'Record')) {
            $this->markTestSkipped("The $class class policy allows everyone to always view all records so this test is not necessary.");
        }

        $this->withToken($this->apiKey([]))
            ->getJson("/{$this->endpoint()}")
            ->assertForbidden();
    }

    public function test_cannot_reach_show_endpoint_with_missing_scope(): void
    {
        $class = class_basename($this->model());

        if (Str::contains($class, 'Record')) {
            $this->markTestSkipped("The $class class policy allows the user to always view records associated with the user so this test is not necessary.");
        }

        $this->withToken($this->apiKey([]))
            ->getJson("/{$this->endpoint()}/{$this->factory->getKey()}")
            ->assertForbidden();
    }

    public function test_cannot_reach_store_endpoint_with_missing_scope(): void
    {
        $this->withToken($this->apiKey([]))
            ->postJson("/{$this->endpoint()}", $this->storeData())
            ->assertForbidden();
    }

    public function test_cannot_reach_update_endpoint_with_missing_scope(): void
    {
        $this->withToken($this->apiKey([]))
            ->patchJson("/{$this->endpoint()}/{$this->factory->getKey()}", $this->updateData())
            ->assertForbidden();
    }

    public function test_cannot_reach_delete_endpoint_with_missing_scope(): void
    {
        $this->withToken($this->apiKey([]))
            ->deleteJson("/{$this->endpoint()}/{$this->factory->getKey()}")
            ->assertForbidden();
    }

    public function test_cannot_reach_store_endpoint_with_missing_body(): void
    {
        $this->withToken($this->apiKey($this->scopes()['store']))
            ->postJson("/{$this->endpoint()}")
            ->assertStatus(422);
    }

    public function test_show_endpoint_returns_not_found(): void
    {
        $this->withToken($this->apiKey($this->scopes()['show']))
            ->getJson("/{$this->endpoint()}/{$this->faker->randomDigitNot($this->factory->getKey())}")
            ->assertNotFound();
    }

    public function test_sortable_fields_match_table_columns(): void
    {
        $controller = $this->app->make($this->controller());

        $model = $this->newModelFor($this->model());

        $required = array_diff(Schema::getColumnListing($this->getTable($this->model())), $model->getHidden());

        $intersection = array_intersect($controller->sortableBy(), $required);

        $this->assertIsArray($controller->sortableBy());
        $this->assertCount(count($required), $intersection);
    }

    public function test_searchable_fields_match_table_columns(): void
    {
        $controller = $this->app->make($this->controller());

        $model = $this->newModelFor($this->model());

        $required = array_diff(Schema::getColumnListing($this->getTable($this->model())), $model->getHidden());

        $intersection = array_intersect($controller->searchableBy(), $required);

        $this->assertIsArray($controller->sortableBy());
        $this->assertCount(count($required), $intersection);
    }

    public function test_filterable_fields_match_table_columns(): void
    {
        $controller = $this->app->make($this->controller());

        $model = $this->newModelFor($this->model());

        $required = array_diff(Schema::getColumnListing($this->getTable($this->model())), $model->getHidden());

        $intersection = array_intersect($controller->filterableBy(), $required);

        $this->assertIsArray($controller->sortableBy());
        $this->assertCount(count($required), $intersection);
    }
}
