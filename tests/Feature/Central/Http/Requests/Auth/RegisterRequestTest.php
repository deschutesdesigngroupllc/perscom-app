<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Http\Requests\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Domain;
use App\Models\Tenant;
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

    public function test_the_founders_name_is_required(): void
    {
        config(['services.cloudflare.turnstile.site_key' => null]);

        $validator = Validator::make($this->payload(['name' => '']), (new RegisterRequest)->rules());

        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_a_reserved_or_malformed_subdomain_is_rejected(): void
    {
        config(['services.cloudflare.turnstile.site_key' => null]);

        foreach (['www', 'Not Valid!'] as $subdomain) {
            $validator = Validator::make($this->payload(['subdomain' => $subdomain]), (new RegisterRequest)->rules());

            $this->assertArrayHasKey('subdomain', $validator->errors()->toArray(), "Expected [{$subdomain}] to be rejected.");
        }
    }

    public function test_a_subdomain_already_in_use_is_rejected(): void
    {
        config(['services.cloudflare.turnstile.site_key' => null]);

        Domain::factory()->createQuietly(['domain' => 'taken-unit', 'tenant_id' => Tenant::factory()->createQuietly()->getKey()]);

        $validator = Validator::make($this->payload(['subdomain' => 'taken-unit']), (new RegisterRequest)->rules());

        $this->assertArrayHasKey('subdomain', $validator->errors()->toArray());
    }

    public function test_an_unknown_organization_type_is_rejected(): void
    {
        config(['services.cloudflare.turnstile.site_key' => null]);

        $validator = Validator::make($this->payload(['organization_type' => 'pirates']), (new RegisterRequest)->rules());

        $this->assertArrayHasKey('organization_type', $validator->errors()->toArray());
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'organization' => 'Test Organization '.fake()->unique()->numerify('####'),
            'name' => 'Jordan Reyes',
            'email' => fake()->unique()->safeEmail(),
            'privacy' => true,
            'token' => 'turnstile-token',
        ], $overrides);
    }
}
