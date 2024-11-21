<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Announcements\AnnouncementsController;
use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'announcements';
    }

    public function controller(): string
    {
        return AnnouncementsController::class;
    }

    public function model(): string
    {
        return Announcement::class;
    }

    /**
     * @return Factory<Announcement>
     */
    public function factory(): Factory
    {
        return Announcement::factory();
    }

    /**
     * @return string[]
     */
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

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'title' => 'Test Announcement',
            'content' => $this->faker->paragraph,
            'color' => 'success',
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [
            'title' => $this->faker->name,
        ];
    }
}
