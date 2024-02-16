<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\CombatRecords\CombatRecordsController;
use App\Models\CombatRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class CombatRecordTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'combat-records';
    }

    public function controller(): string
    {
        return CombatRecordsController::class;
    }

    public function model(): string
    {
        return CombatRecord::class;
    }

    public function factory(): Factory
    {
        return CombatRecord::factory()->state([
            'user_id' => $this->user->getKey(),
        ]);
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:combatrecord',
            'show' => 'view:combatrecord',
            'store' => 'create:combatrecord',
            'update' => 'update:combatrecord',
            'delete' => 'delete:combatrecord',
        ];
    }

    public function storeData(): array
    {
        return [
            'user_id' => $this->user->getKey(),
            'author_id' => $this->user->getKey(),
            'text' => $this->faker->paragraph,
        ];
    }

    public function updateData(): array
    {
        return [
            'text' => $this->faker->paragraph,
        ];
    }
}
