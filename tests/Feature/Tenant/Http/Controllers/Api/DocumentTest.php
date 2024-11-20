<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Documents\DocumentsController;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'documents';
    }

    public function controller(): string
    {
        return DocumentsController::class;
    }

    public function model(): string
    {
        return Document::class;
    }

    public function factory(): Factory
    {
        return Document::factory();
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:document',
            'show' => 'view:document',
            'store' => 'create:document',
            'update' => 'update:document',
            'delete' => 'delete:document',
        ];
    }

    public function storeData(): array
    {
        return [
            'author_id' => User::factory()->create()->getKey(),
            'name' => $this->faker->word,
            'content' => $this->faker->paragraph,
        ];
    }

    public function updateData(): array
    {
        return [
            'content' => $this->faker->paragraph,
        ];
    }
}
