<?php

declare(strict_types=1);

namespace App\Actions\Tenant;

use App\Actions\Organization\ApplyOrganizationBlueprint;
use App\Data\Organization\OrganizationBlueprintData;
use App\Mail\Tenant\NewTenantMail;
use App\Models\Announcement;
use App\Models\Enums\InviteType;
use App\Models\Enums\OrganizationType;
use App\Models\Invite;
use App\Models\Page;
use App\Models\Tenant;
use App\Models\User;
use App\Settings\DashboardSettings;
use App\Settings\OnboardingSettings;
use App\Settings\OrganizationSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Validation\ValidationException;

class SetupTenantAccount
{
    use Conditionable;

    protected Tenant $tenant;

    protected bool $shouldSeedSettings = true;

    protected bool $shouldSetInitialSettings = true;

    protected bool $shouldCreateUser = true;

    protected bool $shouldApplyBlueprint = true;

    protected bool $shouldCreateNewsItem = true;

    protected bool $shouldCreateAnnouncement = true;

    protected bool $shouldCreatePages = true;

    public function handle(Tenant $tenant): void
    {
        $this->tenant = $tenant;

        $this->tenant->run(function (): void {
            $this->when($this->shouldSeedSettings, fn () => $this->seedInitialSettings());
            $this->when($this->shouldSetInitialSettings, fn () => $this->setInitialSettings());
            $this->when($this->shouldApplyBlueprint, fn () => $this->applyOrganizationBlueprint());
            $this->when($this->shouldCreateUser, fn () => $this->createInitialUserAccount());
            $this->when($this->shouldCreateNewsItem, fn () => $this->createFirstNewsfeedItem());
            $this->when($this->shouldCreateAnnouncement, fn () => $this->createAnnouncement());
            $this->when($this->shouldCreatePages, fn () => $this->createPages());
        });

        $this->tenant->update([
            'setup_completed_at' => now(),
        ]);
    }

    public function shouldSeedSettings(bool $seedSettings = true): static
    {
        $this->shouldSeedSettings = $seedSettings;

        return $this;
    }

    public function shouldSetInitialSettings(bool $initialSettings = true): static
    {
        $this->shouldSetInitialSettings = $initialSettings;

        return $this;
    }

    public function shouldCreateUser(bool $createUser = true): static
    {
        $this->shouldCreateUser = $createUser;

        return $this;
    }

    public function shouldApplyBlueprint(bool $applyBlueprint = true): static
    {
        $this->shouldApplyBlueprint = $applyBlueprint;

        return $this;
    }

    public function shouldCreateNewsItem(bool $createNewsItem = true): static
    {
        $this->shouldCreateNewsItem = $createNewsItem;

        return $this;
    }

    public function shouldCreateAnnouncement(bool $createAnnouncement = true): static
    {
        $this->shouldCreateAnnouncement = $createAnnouncement;

        return $this;
    }

    public function shouldCreatePages(bool $createPages = true): static
    {
        $this->shouldCreatePages = $createPages;

        return $this;
    }

    protected function seedInitialSettings(): void
    {
        Artisan::call('tenants:migrate', [
            '--tenants' => [$this->tenant->getTenantKey()],
            '--path' => database_path('settings/tenant'),
            '--force' => true,
        ]);
    }

    protected function setInitialSettings(): void
    {
        /** @var OrganizationSettings $organization */
        $organization = resolve(OrganizationSettings::class);
        $organization->name = $this->tenant->name;
        $organization->email = $this->tenant->email;
        $organization->save();

        /** @var DashboardSettings $dashboard */
        $dashboard = resolve(DashboardSettings::class);
        $dashboard->title = $this->tenant->name;
        $dashboard->save();

        $requestedSubdomain = $this->tenant->getAttribute('requested_subdomain');

        if (filled($requestedSubdomain)) {
            UpdateTenantSubdomain::handle($this->tenant, (string) $requestedSubdomain);
        }
    }

    /**
     * Start the organization with the structure that matches the type chosen at signup.
     */
    protected function applyOrganizationBlueprint(): void
    {
        $type = OrganizationType::tryFrom((string) $this->tenant->getAttribute('organization_type'));
        $preset = $type?->blueprintPreset();

        if (blank($preset) || ! array_key_exists($preset, OrganizationBlueprintData::presets())) {
            return;
        }

        ApplyOrganizationBlueprint::handle(OrganizationBlueprintData::fromPreset($preset));

        // The structure the setup wizard would build already exists, so it shouldn't open; the dashboard's
        // Launch Plan carries the owner through the rest.
        resolve(OnboardingSettings::class)->markCompleted();
    }

    /**
     * Create the founder's account and email them a one-time link to choose their own password.
     *
     * @throws ValidationException
     */
    protected function createInitialUserAccount(): void
    {
        if (User::query()->where('email', $this->tenant->email)->exists()) {
            return;
        }

        // The founder starts without a password: a claim invite can only be redeemed while the profile is
        // unclaimed, and redeeming it is how they choose one.
        $user = User::query()->create([
            'name' => filled($this->tenant->getAttribute('founder_name')) ? (string) $this->tenant->getAttribute('founder_name') : 'Admin',
            'email' => $this->tenant->email,
        ]);
        $user->forceFill(['email_verified_at' => now(), 'approved' => true])->save();
        $user->assignRole(Utils::getSuperAdminName());

        $invite = Invite::create([
            'type' => InviteType::CLAIM,
            'email' => $user->email,
            'name' => $user->name,
            'user_id' => $user->getKey(),
            'token' => Invite::hashToken(Str::random(64)),
        ]);

        $tenant = $this->tenant->refresh();
        $setPasswordUrl = rtrim((string) $tenant->url, '/').route('app.invites.accept', ['token' => $invite->refreshToken()], false);

        Mail::to($user)->send(new NewTenantMail($tenant, $user, $setPasswordUrl));
    }

    protected function createFirstNewsfeedItem(): void
    {
        activity('newsfeed')
            ->withProperties([
                'text' => "Welcome to our platform! We're thrilled to have you on board. Get ready to experience a powerful and intuitive solution that streamlines personnel management and transforms how you organize and track your team. For more information and tutorials, please visit our documentation at <a href='https://docs.perscom.io' target='_blank'>https://docs.perscom.io</a>.",
                'headline' => 'Welcome to PERSCOM Personnel Management System, '.$this->tenant->name,
            ])
            ->event('created')
            ->causedBy(User::first())
            ->log('created');
    }

    protected function createAnnouncement(): void
    {
        if (Announcement::query()->where('global', true)->exists()) {
            return;
        }

        Announcement::create([
            'title' => 'Welcome to PERSCOM Personnel Management System, '.$this->tenant->name,
            'content' => '<p>Take a look around, and if you have any questions, please join our Slack at <a href="https://perscom.io/slack">https://perscom.io/slack</a>.</p>',
            'color' => 'info',
            'global' => true,
        ]);
    }

    protected function createPages(): void
    {
        $pages = [
            [
                'name' => 'Awards',
                'description' => 'This is a custom page that has been built to display awards using the PERSCOM widgets.',
                'slug' => 'awards',
                'hidden' => false,
                'icon' => Heroicon::OutlinedTrophy,
                'content' => <<<'HTML_WRAP'
                <!-- This HTML uses Alpine.js to dynamically build the widget -->
                <div x-data="{
                      init() {
                        const script = document.createElement('script');
                        script.id = 'perscom_widget';
                        script.src = '{{ widgetUrl() }}';
                        script.type = 'text/javascript';
                        script.setAttribute('data-apikey', '{{ ssoJwt() }}');
                        script.setAttribute('data-widget', 'awards');
                
                        if (document.documentElement.classList.contains('dark')) {
                            script.setAttribute('data-dark', 'true');
                        }
                
                        document.getElementById('perscom_widget_wrapper')?.appendChild(script);
                      }
                  }" x-init="init">
                    <div id="perscom_widget_wrapper"></div>
                </div>
                HTML_WRAP
            ],
            [
                'name' => 'Positions',
                'description' => 'This is a custom page that has been built to display positions using the PERSCOM widgets.',
                'slug' => 'positions',
                'hidden' => false,
                'icon' => Heroicon::OutlinedIdentification,
                'content' => <<<'HTML_WRAP'
                <!-- This HTML uses Alpine.js to dynamically build the widget -->
                <div x-data="{
                      init() {
                        const script = document.createElement('script');
                        script.id = 'perscom_widget';
                        script.src = '{{ widgetUrl() }}';
                        script.type = 'text/javascript';
                        script.setAttribute('data-apikey', '{{ ssoJwt() }}');
                        script.setAttribute('data-widget', 'positions');
                
                        if (document.documentElement.classList.contains('dark')) {
                            script.setAttribute('data-dark', 'true');
                        }
                
                        document.getElementById('perscom_widget_wrapper')?.appendChild(script);
                      }
                  }" x-init="init">
                    <div id="perscom_widget_wrapper"></div>
                </div>
                HTML_WRAP
            ],
            [
                'name' => 'Qualifications',
                'description' => 'This is a custom page that has been built to display qualifications using the PERSCOM widgets.',
                'slug' => 'qualifications',
                'hidden' => false,
                'icon' => Heroicon::OutlinedStar,
                'content' => <<<'HTML_WRAP'
                <!-- This HTML uses Alpine.js to dynamically build the widget -->
                <div x-data="{
                      init() {
                        const script = document.createElement('script');
                        script.id = 'perscom_widget';
                        script.src = '{{ widgetUrl() }}';
                        script.type = 'text/javascript';
                        script.setAttribute('data-apikey', '{{ ssoJwt() }}');
                        script.setAttribute('data-widget', 'qualifications');
                
                        if (document.documentElement.classList.contains('dark')) {
                            script.setAttribute('data-dark', 'true');
                        }
                
                        document.getElementById('perscom_widget_wrapper')?.appendChild(script);
                      }
                  }" x-init="init">
                    <div id="perscom_widget_wrapper"></div>
                </div>
                HTML_WRAP
            ],
            [
                'name' => 'Ranks',
                'description' => 'This is a custom page that has been built to display ranks using the PERSCOM widgets.',
                'slug' => 'ranks',
                'hidden' => false,
                'icon' => Heroicon::OutlinedChevronDoubleUp,
                'content' => <<<'HTML_WRAP'
                <!-- This HTML uses Alpine.js to dynamically build the widget -->
                <div x-data="{
                      init() {
                        const script = document.createElement('script');
                        script.id = 'perscom_widget';
                        script.src = '{{ widgetUrl() }}';
                        script.type = 'text/javascript';
                        script.setAttribute('data-apikey', '{{ ssoJwt() }}');
                        script.setAttribute('data-widget', 'ranks');
                
                        if (document.documentElement.classList.contains('dark')) {
                            script.setAttribute('data-dark', 'true');
                        }
                
                        document.getElementById('perscom_widget_wrapper')?.appendChild(script);
                      }
                  }" x-init="init">
                    <div id="perscom_widget_wrapper"></div>
                </div>
                HTML_WRAP
            ],
            [
                'name' => 'Specialties',
                'description' => 'This is a custom page that has been built to display specialties using the PERSCOM widgets.',
                'slug' => 'specialties',
                'hidden' => false,
                'icon' => Heroicon::OutlinedBriefcase,
                'content' => <<<'HTML_WRAP'
                <!-- This HTML uses Alpine.js to dynamically build the widget -->
                <div x-data="{
                      init() {
                        const script = document.createElement('script');
                        script.id = 'perscom_widget';
                        script.src = '{{ widgetUrl() }}';
                        script.type = 'text/javascript';
                        script.setAttribute('data-apikey', '{{ ssoJwt() }}');
                        script.setAttribute('data-widget', 'specialties');
                
                        if (document.documentElement.classList.contains('dark')) {
                            script.setAttribute('data-dark', 'true');
                        }
                
                        document.getElementById('perscom_widget_wrapper')?.appendChild(script);
                      }
                  }" x-init="init">
                    <div id="perscom_widget_wrapper"></div>
                </div>
                HTML_WRAP
            ],
        ];

        foreach ($pages as $page) {
            Page::query()->firstOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
