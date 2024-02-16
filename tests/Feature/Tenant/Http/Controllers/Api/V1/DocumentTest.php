<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Documents\DocumentsController;
use App\Models\Document;
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
