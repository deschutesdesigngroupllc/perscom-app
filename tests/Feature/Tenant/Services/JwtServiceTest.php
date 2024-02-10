<?php

namespace Tests\Feature\Tenant\Services;

use App\Auth\Providers\CustomJwtProvider;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\Feature\Tenant\TenantTestCase;

class JwtServiceTest extends TenantTestCase
{
    public function test_returns_true_when_signed_by_perscom()
    {
        Auth::guard('jwt')->login(User::factory()->create());

        $this->assertTrue(JwtService::signedByPerscom(Auth::guard('jwt')->getToken()));
    }

    public function test_returns_false_when_not_signed_by_perscom()
    {
        $this->instance(
            'tymon.jwt.provider.jwt.lcobucci',
            new CustomJwtProvider(
                Str::random(40),
                $this->app->make('config')->get('jwt.algo'),
                $this->app->make('config')->get('jwt.keys')
            )
        );

        Auth::guard('jwt')->login(User::factory()->create());

        $this->assertFalse(JwtService::signedByPerscom(Auth::guard('jwt')->getToken()));
    }
}
