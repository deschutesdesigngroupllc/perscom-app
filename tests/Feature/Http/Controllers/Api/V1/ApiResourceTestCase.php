<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Laravel\Passport\Passport;
use Tests\Contracts\ApiResourceTestContract;
use Tests\Feature\Http\Controllers\Api\ApiTestCase;

abstract class ApiResourceTestCase extends ApiTestCase implements ApiResourceTestContract
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    protected $factory;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->factory()->create();
    }

    public function test_can_reach_index_endpoint()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['index'],
        ]);

        $this->getJson("/{$this->endpoint()}")
             ->assertSuccessful();
    }

    public function test_can_reach_show_endpoint()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['show'],
        ]);

        $this->getJson("/{$this->endpoint()}/{$this->factory->getKey()}")
             ->assertSuccessful();
    }

    public function test_can_reach_store_endpoint()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['store'],
        ]);

        $data = $this->storeData();

        $this->postJson("/{$this->endpoint()}", $data)
             ->assertSuccessful();

        $this->assertDatabaseHas($this->endpoint(), $data);
    }

    public function test_can_reach_update_endpoint()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['update'],
        ]);

        $data = $this->updateData();

        $this->patchJson("/{$this->endpoint()}/{$this->factory->getKey()}", $data)
             ->assertSuccessful();

        $this->assertDatabaseHas($this->endpoint(), $data);
    }

    public function test_can_reach_delete_endpoint()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['delete'],
        ]);

        $this->deleteJson("/{$this->endpoint()}/{$this->factory->getKey()}")
             ->assertSuccessful();

        $this->assertDatabaseMissing($this->endpoint(), [
            'id' => $this->factory->getKey(),
        ]);
    }

    public function test_cannot_reach_index_endpoint_with_missing_scope()
    {
        Passport::actingAs($this->user);

        $this->getJson("/{$this->endpoint()}")
             ->assertForbidden();
    }

    public function test_cannot_reach_show_endpoint_with_missing_scope()
    {
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

    public function test_show_store_endpoint_returns_not_found()
    {
        Passport::actingAs($this->user, [
            $this->scopes()['show'],
        ]);

        $this->getJson("/{$this->endpoint()}/{$this->faker->randomDigitNot($this->factory->getKey())}")
             ->assertNotFound();
    }
}
