<?php

namespace Tests\Feature\Central\Requests;

use Tests\Feature\Central\CentralTestCase;

class CentralRequestTest extends CentralTestCase
{
    public function test_request_is_recognized_as_central()
    {
        $this->get(route('login'))
            ->assertSuccessful();

        $this->assertTrue(\Request::isCentralRequest());
    }

    public function test_request_properly_configures_fortify()
    {
        $this->get(route('login'))
            ->assertSuccessful();

        $this->assertSame('admin', config('fortify.guard'));
        $this->assertSame('admins', config('fortify.passwords'));
        $this->assertSame('/admin', config('fortify.prefix'));
        $this->assertSame([], config('fortify.features'));
    }
}
