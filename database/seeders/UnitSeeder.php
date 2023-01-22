<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::factory()->count(5)->sequence(fn ($sequence) => [
            'name' => 'Unit '.$sequence->index + 1
        ])->create();
    }
}
