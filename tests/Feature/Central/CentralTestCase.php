<?php

namespace Tests\Feature\Central;

use App\Models\Admin;
use Tests\TestCase;

class CentralTestCase extends TestCase
{
    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();
    }
}
