<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Support\Passport;

use App\Models\User;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Plain;
use League\OAuth2\Server\Exception\OAuthServerException;
use Tests\Feature\Tenant\TenantTestCase;

class AccessTokenTest extends TenantTestCase
{
    public function test_it_errors_on_incorrect_claims()
    {
        $this->expectException(OAuthServerException::class);

        $user = User::factory()->create();

        $user->createToken($this->faker->word, [
            $this->faker->word,
        ]);
    }

    public function test_it_adds_all_the_claims()
    {
        $user = User::factory()->create();

        $result = $user->createToken($this->faker->word, [
            'view:user',
            'create:user',
        ]);

        $parser = new Parser(new JoseEncoder());

        /** @var Plain $token */
        $token = $parser->parse($result->accessToken);

        $claims = $token->claims()->all();

        $this->assertTrue(in_array('view:user', data_get($claims, 'scopes')));
        $this->assertTrue(in_array('create:user', data_get($claims, 'scopes')));
        $this->assertSame((string) $user->getKey(), data_get($claims, 'sub'));
        $this->assertSame($user->name, data_get($claims, 'name'));
        $this->assertSame($user->email, data_get($claims, 'preferred_username'));
        $this->assertSame($user->url, data_get($claims, 'profile'));
        $this->assertSame($user->email, data_get($claims, 'email'));
        $this->assertSame(! is_null($user->email_verified_at), data_get($claims, 'email_verified'));
        $this->assertSame($user->profile_photo_url, data_get($claims, 'picture'));
        $this->assertSame((string) $this->tenant->getKey(), data_get($claims, 'tenant'));
        $this->assertSame(config('app.locale'), data_get($claims, 'locale'));
        $this->assertSame(config('app.timezone'), data_get($claims, 'zoneinfo'));
        $this->assertSame($user->updated_at->getTimestamp(), data_get($claims, 'updated_at'));
    }
}
