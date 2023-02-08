<?php

namespace Database\Seeders\Demo\Military;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $tasks = [
            [
                'title' => 'Submit After Action Report',
            ],
            [
                'title' => 'Update Personal Information',
            ],
            [
                'title' => 'Attend Promotion Ceremony',
            ],
        ];

        foreach ($tasks as $task) {
            Task::factory()->state($task)->create();
        }
    }
}
