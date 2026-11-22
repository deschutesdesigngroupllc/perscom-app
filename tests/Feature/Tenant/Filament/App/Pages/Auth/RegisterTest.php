<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages\Auth;

use App\Filament\App\Pages\Auth\Login;
use App\Filament\App\Pages\Auth\Register;
use App\Settings\RegistrationSettings;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class RegisterTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('app');
    }

    public function test_registration_page_renders_when_registration_is_enabled(): void
    {
        RegistrationSettings::fake(['enabled' => true, 'admin_approval_required' => false]);

        Livewire::test(Register::class)
            ->assertOk();
    }

    public function test_registration_page_is_not_found_when_registration_is_disabled(): void
    {
        RegistrationSettings::fake(['enabled' => false, 'admin_approval_required' => false]);

        Livewire::test(Register::class)
            ->assertNotFound();
    }

    public function test_login_page_hides_sign_up_link_when_registration_is_disabled(): void
    {
        RegistrationSettings::fake(['enabled' => false, 'admin_approval_required' => false]);

        Livewire::test(Login::class)
            ->assertDontSeeHtml((string) Filament::getRegistrationUrl());
    }

    public function test_login_page_shows_sign_up_link_when_registration_is_enabled(): void
    {
        RegistrationSettings::fake(['enabled' => true, 'admin_approval_required' => false]);

        Livewire::test(Login::class)
            ->assertSeeHtml((string) Filament::getRegistrationUrl());
    }

    public function test_social_registration_follows_the_registration_setting(): void
    {
        /** @var FilamentSocialitePlugin $plugin */
        $plugin = Filament::getPanel('app')->getPlugin('filament-socialite');

        RegistrationSettings::fake(['enabled' => false, 'admin_approval_required' => false]);
        $this->assertFalse(value($plugin->getRegistration()));

        RegistrationSettings::fake(['enabled' => true, 'admin_approval_required' => false]);
        $this->assertTrue(value($plugin->getRegistration()));
    }

    public function test_only_configured_social_providers_are_offered(): void
    {
        /** @var FilamentSocialitePlugin $plugin */
        $plugin = Filament::getPanel('app')->getPlugin('filament-socialite');

        $names = collect($plugin->getProviders())->keys()->all();

        collect(['google', 'discord', 'github'])->each(function (string $name) use ($names): void {
            $configured = filled(config("services.{$name}.client_id")) && filled(config("services.{$name}.client_secret"));

            $this->assertSame($configured, in_array($name, $names, true), "Provider [{$name}] visibility should match its configuration.");
        });
    }
}
