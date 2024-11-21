<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Forms\FormsController;
use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FormTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'forms';
    }

    public function controller(): string
    {
        return FormsController::class;
    }

    public function model(): string
    {
        return Form::class;
    }

    /**
     * @return Factory<Form>
     */
    public function factory(): Factory
    {
        return Form::factory();
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:form',
            'show' => 'view:form',
            'store' => 'create:form',
            'update' => 'update:form',
            'delete' => 'delete:form',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'name' => $this->faker->word,
            'slug' => Str::slug($this->faker->word),
            'is_public' => false,
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [
            'description' => $this->faker->paragraph,
        ];
    }
}
