<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Http\Requests\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Tests\Feature\Central\CentralTestCase;

class RegisterRequestTest extends CentralTestCase
{
    public function test_a_rejected_turnstile_token_fails_validation(): void
    {
        config(['services.cloudflare.turnstile.site_key' => 'site-key']);
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false])]);

        $validator = Validator::make($this->payload(), (new RegisterRequest)->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('token', $validator->errors()->toArray());
    }

    public function test_a_verified_turnstile_token_passes_validation(): void
    {
        config(['services.cloudflare.turnstile.site_key' => 'site-key']);
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);

        $validator = Validator::make($this->payload(), (new RegisterRequest)->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_turnstile_is_not_required_when_it_is_not_configured(): void
    {
        config(['services.cloudflare.turnstile.site_key' => null]);
        Http::fake();

        $validator = Validator::make($this->payload(['token' => null]), (new RegisterRequest)->rules());

        $this->assertFalse($validator->fails());
        Http::assertNothingSent();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'organization' => 'Test Organization '.fake()->unique()->numerify('####'),
            'email' => fake()->unique()->safeEmail(),
            'privacy' => true,
            'token' => 'turnstile-token',
        ], $overrides);
    }
}
