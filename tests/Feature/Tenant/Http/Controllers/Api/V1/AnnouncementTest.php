<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'announcements';
    }

    public function model(): string
    {
        return Announcement::class;
    }

    public function factory(): Factory
    {
        return Announcement::factory();
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:announcement',
            'show' => 'view:announcement',
            'store' => 'create:announcement',
            'update' => 'update:announcement',
            'delete' => 'delete:announcement',
        ];
    }

    public function storeData(): array
    {
        return [
            'title' => 'Test Announcement',
            'content' => $this->faker->paragraph,
            'color' => 'success',
        ];
    }

    public function updateData(): array
    {
        return [
            'title' => $this->faker->name,
        ];
    }
}
