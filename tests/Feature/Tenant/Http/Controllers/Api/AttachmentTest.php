<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Attachments\AttachmentsController;
use App\Models\Attachment;
use App\Models\ServiceRecord;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class AttachmentTest extends ApiResourceTestCase
{
    public function beforeTestCanReachIndexEndpoint(): void
    {
        $this->user->assignRole(Utils::getSuperAdminName());
    }

    public function endpoint(): string
    {
        return 'attachments';
    }

    public function model(): string
    {
        return Attachment::class;
    }

    public function controller(): string
    {
        return AttachmentsController::class;
    }

    public function factory(): Factory
    {
        return Attachment::factory()
            ->afterMaking(function (Attachment $attachment) {
                $attachment->model()->associate(ServiceRecord::factory()->create());
            });
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:servicerecord',
            'show' => 'view:servicerecord',
            'store' => 'create:attachment',
            'update' => 'update:servicerecord',
            'delete' => 'delete:servicerecord',
        ];
    }

    public function storeData(): array
    {
        return [
            'name' => $this->faker->word,
            'file' => UploadedFile::fake()->create('data.pdf', 10),
        ];
    }

    public function updateData(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
