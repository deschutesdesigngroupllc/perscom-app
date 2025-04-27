<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Credentials\CredentialsController;
use App\Models\Credential;
use App\Models\Enums\CredentialType;
use App\Models\Group;
use App\Models\Issuer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CredentialTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'credentials';
    }

    public function controller(): string
    {
        return CredentialsController::class;
    }

    public function model(): string
    {
        return Credential::class;
    }

    /**
     * @return Factory<Group>
     */
    public function factory(): Factory
    {
        return Credential::factory();
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:credential',
            'show' => 'view:credential',
            'store' => 'create:credential',
            'update' => 'update:credential',
            'delete' => 'delete:credential',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => 'Test Credential',
            'description' => $this->faker->sentence,
            'issuer_id' => Issuer::factory()->create()->getKey(),
            'type' => CredentialType::Other->value,
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
