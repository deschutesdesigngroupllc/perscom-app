<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Images\ImagesController;
use App\Models\Award;
use App\Models\Image;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class ImageTest extends ApiResourceTestCase
{
    public function beforeTestCanReachIndexEndpoint(): void
    {
        $this->user->assignRole(Utils::getSuperAdminName());
    }

    public function endpoint(): string
    {
        return 'images';
    }

    public function model(): string
    {
        return Image::class;
    }

    public function controller(): string
    {
        return ImagesController::class;
    }

    public function factory(): Factory
    {
        return Image::factory()
            ->afterMaking(function (Image $image) {
                $image->model()->associate(Award::factory()->create());
            });
    }

    public function scopes(): array
    {
        return [
            'index' => 'view:award',
            'show' => 'view:award',
            'store' => 'create:image',
            'update' => 'update:award',
            'delete' => 'delete:award',
        ];
    }

    public function storeData(): array
    {
        return [
            'name' => $this->faker->word,
            'image' => UploadedFile::fake()->create('image.png', 10),
        ];
    }

    public function updateData(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
