<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Submission;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    public function test_virtual_column_can_be_set(): void
    {
        $submission = Submission::factory()->state([
            'user_id' => null,
            'form_id' => null,
            'foo' => 'bar',
        ])->make();

        $this->assertEquals('bar', $submission->foo);
    }
}
