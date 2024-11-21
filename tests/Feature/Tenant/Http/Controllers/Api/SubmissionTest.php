<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Controllers\Api\Submissions\SubmissionsController;
use App\Models\Form;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionTest extends ApiResourceTestCase
{
    public function endpoint(): string
    {
        return 'submissions';
    }

    public function controller(): string
    {
        return SubmissionsController::class;
    }

    public function model(): string
    {
        return Submission::class;
    }

    /**
     * @return Factory<Submission>
     */
    public function factory(): Factory
    {
        return Submission::factory()->state([
            'user_id' => $this->user->getKey(),
        ]);
    }

    /**
     * @return string[]
     */
    public function scopes(): array
    {
        return [
            'index' => 'view:submission',
            'show' => 'view:submission',
            'store' => 'create:submission',
            'update' => 'update:submission',
            'delete' => 'delete:submission',
        ];
    }

    /**
     * @return string[]
     */
    public function storeData(): array
    {
        return [
            'form_id' => Form::factory()->create()->getKey(),
            'user_id' => $this->user->getKey(),
        ];
    }

    /**
     * @return string[]
     */
    public function updateData(): array
    {
        return [];
    }
}
