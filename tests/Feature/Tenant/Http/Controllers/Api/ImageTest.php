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

    public function beforeAssertDatabaseHas(array &$data): void
    {
        data_forget($data, 'image');
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

    /**
     * @return Factory<Image>
     */
    public function factory(): Factory
    {
        return Image::factory()
            ->afterMaking(function (Image $image): void {
                $image->model()->associate(Award::factory()->create());
            });
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:image',
            'show' => 'view:image',
            'store' => 'create:image',
            'update' => 'update:image',
            'delete' => 'delete:image',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => $this->faker->word,
            'image' => UploadedFile::fake()->create('image.png', 10),
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
