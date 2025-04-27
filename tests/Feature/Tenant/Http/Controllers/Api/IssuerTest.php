<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Issuers\IssuersController;
use App\Models\Group;
use App\Models\Issuer;
use Illuminate\Database\Eloquent\Factories\Factory;

class IssuerTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'issuers';
    }

    public function controller(): string
    {
        return IssuersController::class;
    }

    public function model(): string
    {
        return Issuer::class;
    }

    /**
     * @return Factory<Group>
     */
    public function factory(): Factory
    {
        return Issuer::factory();
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:issuer',
            'show' => 'view:issuer',
            'store' => 'create:issuer',
            'update' => 'update:issuer',
            'delete' => 'delete:issuer',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => 'Test Issuer',
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
