<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Organization;

use App\Actions\Organization\CreateCredential;
use App\Models\Credential;
use App\Models\Enums\CredentialType;
use App\Models\Issuer;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Tenant\TenantTestCase;

class CreateCredentialTest extends TenantTestCase
{
    public function test_it_creates_a_credential_and_its_issuer(): void
    {
        $credential = CreateCredential::handle([
            'name' => 'Commercial Driver License',
            'type' => 'license',
            'issuer' => 'Department of Transportation',
        ]);

        $this->assertTrue($credential->wasRecentlyCreated);
        $this->assertSame(CredentialType::LICENSE, $credential->type);
        $this->assertSame('Department of Transportation', $credential->issuer->name);
    }

    public function test_it_reuses_an_existing_issuer_and_credential(): void
    {
        $issuer = Issuer::factory()->createQuietly(['name' => 'Department of Transportation']);

        $first = CreateCredential::handle(['name' => 'Commercial Driver License', 'issuer' => 'department of transportation']);
        $second = CreateCredential::handle(['name' => 'commercial driver license']);

        $this->assertSame($issuer->getKey(), $first->issuer_id);
        $this->assertTrue($second->is($first));
        $this->assertSame(1, Credential::query()->count());
        $this->assertSame(1, Issuer::query()->count());
    }

    public function test_it_defaults_to_a_certification(): void
    {
        $credential = CreateCredential::handle(['name' => 'First Aid']);

        $this->assertSame(CredentialType::CERTIFICATION, $credential->type);
        $this->assertNull($credential->issuer_id);
    }

    public function test_it_rejects_an_unknown_type(): void
    {
        $this->expectException(ValidationException::class);

        CreateCredential::handle(['name' => 'First Aid', 'type' => 'diploma']);
    }
}
