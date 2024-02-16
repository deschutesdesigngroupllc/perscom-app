<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\Contracts\ApiResourceTestContract;
use Tests\Feature\Tenant\Http\Controllers\Api\ApiTestCase;

abstract class ApiResourceTestCase extends ApiTestCase implements ApiResourceTestContract
{
    protected Collection|Model $factory;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->user = User::factory()->create();
        $this->factory = $this->factory()->create();
    }

    public function test_can_reach_index_endpoint()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['index'],
        ]);

        $this->getJson("/{$this->endpoint()}")
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertSuccessful();
    }

    public function test_can_reach_show_endpoint()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['show'],
        ]);

        $this->getJson("/{$this->endpoint()}/{$this->factory->getKey()}")
            ->assertJsonStructure(['data'])
            ->assertSuccessful();
    }

    public function test_can_reach_store_endpoint()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['store'],
        ]);

        $data = $this->storeData();

        $this->postJson("/{$this->endpoint()}", $data)
            ->assertJsonStructure(['data'])
            ->assertSuccessful();

        $this->assertDatabaseHas($this->getTable($this->model()), $data);
    }

    public function test_can_reach_update_endpoint()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['update'],
        ]);

        $data = $this->updateData();

        $this->patchJson("/{$this->endpoint()}/{$this->factory->getKey()}", $data)
            ->assertJsonStructure(['data'])
            ->assertSuccessful();

        $this->assertDatabaseHas($this->getTable($this->model()), $data);
    }

    public function test_can_reach_delete_endpoint()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['delete'],
        ]);

        $this->deleteJson("/{$this->endpoint()}/{$this->factory->getKey()}")
            ->assertJsonStructure(['data'])
            ->assertSuccessful();

        $this->assertDatabaseMissing($this->getTable($this->model()), [
            'id' => $this->factory->getKey(),
        ]);
    }

    public function test_cannot_reach_index_endpoint_with_missing_scope()
    {
        $class = class_basename($this->model());

        if (Str::contains($class, 'Record')) {
            $this->markTestSkipped("The $class class policy allows everyone to always view all records so this test is not necessary.");
        }

        Passport::actingAs($this->user);

        $this->getJson("/{$this->endpoint()}")
            ->assertForbidden();
    }

    public function test_cannot_reach_show_endpoint_with_missing_scope()
    {
        $class = class_basename($this->model());

        if (Str::contains($class, 'Record')) {
            $this->markTestSkipped("The $class class policy allows the user to always view records associated with the user so this test is not necessary.");
        }

        Passport::actingAs($this->user);

        $this->getJson("/{$this->endpoint()}/{$this->factory->getKey()}")
            ->assertForbidden();
    }

    public function test_cannot_reach_store_endpoint_with_missing_scope()
    {
        Passport::actingAs($this->user);

        $this->postJson("/{$this->endpoint()}", $this->storeData())
            ->assertForbidden();
    }

    public function test_cannot_reach_update_endpoint_with_missing_scope()
    {
        Passport::actingAs($this->user);

        $this->patchJson("/{$this->endpoint()}/{$this->factory->getKey()}", $this->updateData())
            ->assertForbidden();
    }

    public function test_cannot_reach_delete_endpoint_with_missing_scope()
    {
        Passport::actingAs($this->user);

        $this->deleteJson("/{$this->endpoint()}/{$this->factory->getKey()}")
            ->assertForbidden();
    }

    public function test_cannot_reach_store_endpoint_with_missing_body()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['store'],
        ]);

        $this->postJson("/{$this->endpoint()}")
            ->assertStatus(422);
    }

    public function test_show_endpoint_returns_not_found()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['show'],
        ]);

        $this->getJson("/{$this->endpoint()}/{$this->faker->randomDigitNot($this->factory->getKey())}")
            ->assertNotFound();
    }

    public function test_sortable_fields_match_table_columns()
    {
        $controller = $this->app->make($this->controller());

        $model = $this->newModelFor($this->model());

        $required = array_diff(Schema::getColumnListing($this->getTable($this->model())), $model->getHidden());

        $intersection = array_intersect($controller->sortableBy(), $required);

        $this->assertIsArray($controller->sortableBy());
        $this->assertCount(count($required), $intersection);
    }

    public function test_searchable_fields_match_table_columns()
    {
        $controller = $this->app->make($this->controller());

        $model = $this->newModelFor($this->model());

        $required = array_diff(Schema::getColumnListing($this->getTable($this->model())), $model->getHidden());

        $intersection = array_intersect($controller->searchableBy(), $required);

        $this->assertIsArray($controller->sortableBy());
        $this->assertCount(count($required), $intersection);
    }

    public function test_filterable_fields_match_table_columns()
    {
        $controller = $this->app->make($this->controller());

        $model = $this->newModelFor($this->model());

        $required = array_diff(Schema::getColumnListing($this->getTable($this->model())), $model->getHidden());

        $intersection = array_intersect($controller->filterableBy(), $required);

        $this->assertIsArray($controller->sortableBy());
        $this->assertCount(count($required), $intersection);
    }
}
