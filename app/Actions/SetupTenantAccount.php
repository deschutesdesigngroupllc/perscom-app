<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\Tenant\NewTenantMail;
use App\Models\Announcement;
use App\Models\Tenant;
use App\Models\User;
use App\Settings\DashboardSettings;
use App\Settings\OrganizationSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
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

    protected bool $shouldCreateNewsItem = true;

    protected bool $shouldCreateAnnouncement = true;

    public function handle(Tenant $tenant): void
    {
        $this->tenant = $tenant;

        $this->tenant->run(function () {
            $this->when($this->shouldSeedSettings, fn () => $this->seedInitialSettings());
            $this->when($this->shouldSetInitialSettings, fn () => $this->setInitialSettings());
            $this->when($this->shouldCreateUser, fn () => $this->createInitialUserAccount());
            $this->when($this->shouldCreateNewsItem, fn () => $this->createFirstNewsfeedItem());
            $this->when($this->shouldCreateAnnouncement, fn () => $this->createAnnouncement());
        });
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
        $organization = app(OrganizationSettings::class);
        $organization->name = $this->tenant->name;
        $organization->email = $this->tenant->email;
        $organization->save();

        /** @var DashboardSettings $dashboard */
        $dashboard = app(DashboardSettings::class);
        $dashboard->title = $this->tenant->name;
        $dashboard->save();
    }

    /**
     * @throws ValidationException
     */
    protected function createInitialUserAccount(): void
    {
        $password = Str::password(length: 16, symbols: false);

        $action = new CreateNewUser;

        $user = $action->create([
            'name' => 'Admin',
            'email' => $this->tenant->email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);
        $user->assignRole(Utils::getSuperAdminName());

        Mail::to($user)->send(new NewTenantMail($this->tenant->refresh(), $user, $password));
    }

    protected function createFirstNewsfeedItem(): void
    {
        activity('newsfeed')
            ->withProperties([
                'text' => "Welcome to our platform! We're thrilled to have you on board as a user. Get ready to experience a powerful and intuitive solution that will streamline your personnel management and revolutionize how you organize and track your team. For more information and tutorials, please visit our documentation available at <a href='https://docs.perscom.io' target='_blank'>https://docs.perscom.io</a>.",
                'headline' => "Welcome to PERSCOM Personnel Management System, {$this->tenant->name}",
            ])
            ->event('created')
            ->causedBy(User::first())
            ->log('created');
    }

    protected function createAnnouncement(): void
    {
        Announcement::create([
            'title' => "Welcome to PERSCOM Personnel Management System, {$this->tenant->name}",
            'content' => 'Take a look around and if you have any questions, please reach out to support@deschutesdesigngroup.com.',
            'color' => 'info',
            'global' => true,
        ]);
    }
}
