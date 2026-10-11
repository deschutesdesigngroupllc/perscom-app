<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Middleware;

use App\Http\Middleware\EnsureMultiFactorAuthenticationIsRequired;
use App\Models\User;
use App\Settings\PermissionSettings;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Tenant\TenantTestCase;

class EnsureMultiFactorAuthenticationIsRequiredTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('app');
    }

    public function test_it_is_the_app_panel_required_middleware(): void
    {
        $panel = Filament::getPanel('app');

        $this->assertTrue($panel->isMultiFactorAuthenticationRequired());
        $this->assertSame(EnsureMultiFactorAuthenticationIsRequired::class, $panel->getMultiFactorAuthenticationRequiredMiddleware());
    }

    public function test_it_passes_through_when_the_organization_does_not_require_mfa(): void
    {
        PermissionSettings::fake([
            'default_roles' => [],
            'default_permissions' => [],
            'require_multi_factor_authentication' => false,
        ]);

        $this->actingAs(User::factory()->createQuietly());

        $middleware = Mockery::mock(EnsureMultiFactorAuthenticationIsRequired::class)->makePartial();

        $response = $middleware->handle(Request::create('/'), fn (): Response => new Response('ok'));

        $this->assertSame('ok', $response->getContent());
        $middleware->shouldHaveReceived('handle')->once();
    }

    public function test_it_redirects_to_set_up_when_required_and_user_has_no_mfa(): void
    {
        PermissionSettings::fake([
            'default_roles' => [],
            'default_permissions' => [],
            'require_multi_factor_authentication' => true,
        ]);

        $this->actingAs(User::factory()->createQuietly());

        $middleware = Mockery::mock(EnsureMultiFactorAuthenticationIsRequired::class)->makePartial();

        $response = $middleware->handle(Request::create('/'), fn (): Response => new Response('ok'));

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(Filament::getSetUpRequiredMultiFactorAuthenticationUrl(), $response->getTargetUrl());
        $middleware->shouldHaveReceived('handle')->once();
    }

    public function test_it_passes_through_when_required_and_user_has_mfa_enabled(): void
    {
        PermissionSettings::fake([
            'default_roles' => [],
            'default_permissions' => [],
            'require_multi_factor_authentication' => true,
        ]);

        $user = User::factory()->createQuietly();
        $user->toggleEmailAuthentication(true);

        $this->actingAs($user);

        $middleware = Mockery::mock(EnsureMultiFactorAuthenticationIsRequired::class)->makePartial();

        $response = $middleware->handle(Request::create('/'), fn (): Response => new Response('ok'));

        $this->assertSame('ok', $response->getContent());
        $middleware->shouldHaveReceived('handle')->once();
    }
}
