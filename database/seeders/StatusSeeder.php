<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            [
                'name' => 'Active',
                'color' => 'bg-green-100 text-green-600',
            ],
            [
                'name' => 'Inactive',
                'color' => 'bg-red-100 text-red-600',
            ],
            [
                'name' => 'On Leave',
                'color' => 'bg-sky-100 text-sky-600',
            ],
        ];

        foreach ($statuses as $status) {
            Status::factory()->state($status)->createQuietly();
        }
    }
}
