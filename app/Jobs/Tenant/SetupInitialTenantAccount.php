<?php

namespace App\Jobs\Tenant;

use App\Models\Role;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Outl1ne\NovaSettings\NovaSettings;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class SetupInitialTenantAccount implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected TenantWithDatabase $tenant)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->setInitialSettings();
        $this->createFirstNewsfeedItem();
    }

    /**
     * @return void
     */
    protected function setInitialSettings()
    {
        $this->tenant->run(function ($tenant) {
            Settings::withoutEvents(static function () use ($tenant) {
                NovaSettings::setSettingValue('organization', $tenant->name);
                NovaSettings::setSettingValue('email', $tenant->email);
                NovaSettings::setSettingValue('timezone', config('app.timezone'));
                NovaSettings::setSettingValue('dashboard_title', $tenant->name);
                NovaSettings::setSettingValue('registration_enabled', true);
                NovaSettings::setSettingValue('default_roles', Role::query()->where('name', 'User')->pluck('id')->toArray());
            });
        });
    }

    /**
     * @return void
     */
    protected function createFirstNewsfeedItem()
    {
        $this->tenant->run(function ($tenant) {
            activity('newsfeed')
                ->withProperties([
                    'text' => "Welcome to our platform! We're thrilled to have you on board as a new user. Get ready to experience a powerful and intuitive solution that will streamline your personnel management and revolutionize how you organize and track your team. For more information and tutorials, please visit our documentation available at https://docs.perscom.io.",
                    'headline' => "Welcome to PERSCOM Personnel Management System {$tenant->name}",
                ])
                ->event('created')
                ->causedBy(User::first())
                ->log('created');
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed($exception): void
    {
        Log::error('Failed to setup initial tenant settings', [
            'exception' => $exception,
        ]);
    }
}
